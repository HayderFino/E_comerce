<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\FactusService;
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

        $factusData = $request->factus_data;
        $cart = $request->cart;

        // Calculamos totales
        $subtotal = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['qty'];
        });
        // Si el precio ya incluye IVA, ajustamos (o si no incluye le sumamos el 19%)
        // Asumiendo que factus requiere 19% como en el código original:
        $tax = $subtotal * 0.19;
        $totalAmount = $subtotal + $tax;

        // 2. Generar Consecutivo Ordenado y Guardar venta localmente
        $lastSale = Sale::latest('id')->first();
        $nextId = $lastSale ? $lastSale->id + 1 : 1;
        $referenceCode = 'POS-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

        $sale = Sale::create([
            'user_id' => auth()->id(),
            'customer_document' => $factusData['docNum'],
            'customer_name' => $factusData['name'],
            'customer_email' => $factusData['email'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $totalAmount,
            'reference_code' => $referenceCode,
            'factus_status' => 'pending',
        ]);

        foreach ($cart as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);
        }

        // Guardar el cliente en el directorio si no existe
        if (!empty($factusData['docNum']) && !empty($factusData['name'])) {
            \App\Models\Customer::firstOrCreate(
                ['document_number' => $factusData['docNum']],
                [
                    'name' => $factusData['name'],
                    'document_type' => $factusData['docType'] ?? '13',
                    'email' => $factusData['email'] ?? null,
                    'phone' => $factusData['phone'] ?? null,
                    'address' => $factusData['address'] ?? null,
                ]
            );
        }

        // 3. Procesar Factura Electrónica
        try {
            $factusService = app(FactusService::class);

            // Reconstruimos el array original de la API de Factus
            $items = collect($cart)->map(function ($item) {
                return [
                    'code_reference' => 'PROD-'.$item['id'],
                    'name' => $item['name'],
                    'quantity' => number_format($item['qty'], 2, '.', ''),
                    'discount_rate' => '0.00',
                    'price' => number_format($item['price'], 2, '.', ''),
                    'unit_measure_code' => '94', // unidad
                    'standard_code' => '999',
                    'taxes' => [
                        [
                            'code' => '01', // IVA
                            'rate' => '19.00',
                        ],
                    ],
                ];
            })->toArray();

            $payload = [
                'reference_code' => $referenceCode,
                'document' => '01', // factura de venta
                'numbering_range_id' => env('FACTUS_NUMBERING_RANGE_ID', 389), // Obtenido del endpoint V2
                'operation_type' => '10', // estándar
                'send_email' => true,
                'payment_details' => [
                    [
                        'payment_form' => $factusData['paymentForm'],
                        'payment_method_code' => $factusData['paymentMethod'],
                        'amount' => number_format($totalAmount, 2, '.', ''),
                    ],
                ],
                'customer' => [
                    'identification_document_code' => $factusData['docType'],
                    'identification' => $factusData['docNum'],
                    'legal_organization_code' => '2', // Asumimos persona natural por simplicidad
                    'names' => $factusData['name'],
                    'address' => !empty($factusData['address']) ? $factusData['address'] : 'No registrada',
                    'email' => $factusData['email'],
                    'phone' => $factusData['phone'] ?? '',
                    'municipality_code' => '68020', // Código DIVIPOLA de Albania, Santander
                    'tribute_code' => 'ZZ',
                    'responsibilities' => ['R-99-PN'],
                ],
                'items' => $items,
            ];

            // 4. Enviar Factura a Factus usando el Servicio
            $response = $factusService->createInvoiceFromSale($sale, $factusData);

            $sale->update([
                'factus_status' => 'success',
                'factus_response' => json_encode($response),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada y factura electrónica enviada correctamente.',
                'factus' => $response,
            ]);

        } catch (\Exception $e) {
            $responseBody = $e instanceof \Illuminate\Http\Client\RequestException 
                ? $e->response->body() 
                : $e->getMessage();

            $sale->update([
                'factus_status' => 'failed',
                'factus_response' => $responseBody,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error validando la factura en Factus.',
                'error' => json_decode($responseBody, true) ?? $responseBody,
            ], 500);
        }
    }
}
