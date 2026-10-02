<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function create()
    {
        return view('billing.create');
    }

    public function process(Request $request)
    {
        // 1. Validar la petición
        $request->validate([
            'cart' => 'required|array|min:1',
            'factus_data' => 'required|array',
        ]);

        // 2. Autenticarse con Factus (Con reintentos para Rate Limit 429)
        $authResponse = \Illuminate\Support\Facades\Http::retry(3, 1000, function ($exception, $request) {
            return $exception instanceof \Illuminate\Http\Client\RequestException && $exception->response->status() === 429;
        })->asForm()->post('https://api-sandbox.factus.com.co/oauth/token', [
            'grant_type' => 'password',
            'client_id' => env('FACTUS_CLIENT_ID'),
            'client_secret' => env('FACTUS_CLIENT_SECRET'),
            'username' => env('FACTUS_USERNAME'),
            'password' => env('FACTUS_PASSWORD'),
        ]);

        if (!$authResponse->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Error autenticando con Factus.',
                'error' => $authResponse->json()
            ], 500);
        }

        $token = $authResponse->json('access_token');

        // 3. Preparar el Payload para Factus
        $factusData = $request->factus_data;
        $cart = $request->cart;

        $items = collect($cart)->map(function ($item, $index) {
            return [
                "code_reference" => "PROD-" . $item['id'],
                "name" => $item['name'],
                "quantity" => number_format($item['qty'], 2, '.', ''),
                "discount_rate" => "0.00",
                "price" => number_format($item['price'], 2, '.', ''),
                "unit_measure_code" => "94", // unidad
                "standard_code" => "999", // adopción del contribuyente
                "taxes" => [
                    [
                        "code" => "01", // IVA
                        "rate" => "19.00"
                    ]
                ]
            ];
        })->toArray();

        // Calculamos totales desde los items
        $totalAmount = collect($cart)->sum(function ($item) {
            return ($item['price'] * $item['qty']) * 1.19; // precio + 19% iva
        });

        // 4. Construir Body
        $payload = [
            "reference_code" => "POS-" . time() . rand(1000, 9999),
            "document" => "01", // factura de venta
            "operation_type" => "10", // estándar
            "send_email" => true,
            "cash_rounding_amount" => "0.00",
            "payment_details" => [
                [
                    "payment_form" => $factusData['paymentForm'],
                    "payment_method_code" => $factusData['paymentMethod'],
                    "amount" => number_format($totalAmount, 2, '.', '')
                ]
            ],
            "customer" => [
                "identification_document_code" => $factusData['docType'],
                "identification" => $factusData['docNum'],
                "legal_organization_code" => "2", // Asumimos persona natural por simplicidad
                "names" => $factusData['name'],
                "address" => "No registrada", // Requerido por la DIAN en algunos casos
                "email" => $factusData['email'],
                "tribute_code" => "ZZ",
                "responsibilities" => ["R-99-PN"],
            ],
            "items" => $items
        ];

        // 5. Enviar Factura a Factus (Con reintentos para Rate Limit 429)
        $billResponse = \Illuminate\Support\Facades\Http::withToken($token)
            ->retry(3, 1000, function ($exception, $request) {
                return $exception instanceof \Illuminate\Http\Client\RequestException && $exception->response->status() === 429;
            })
            ->post('https://api-sandbox.factus.com.co/v1/bills/validate', $payload);

        if (!$billResponse->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Error validando la factura en Factus.',
                'error' => $billResponse->json(),
                'payload_enviado' => $payload
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Venta registrada y factura electrónica enviada correctamente.',
            'factus' => $billResponse->json()
        ]);
    }
}
