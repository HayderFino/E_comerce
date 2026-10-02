<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\FactusService;
use Illuminate\Http\Client\RequestException;
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

        // 2. Guardar venta localmente — el reference_code se genera DESPUÉS de obtener el ID real
        $sale = Sale::create([
            'user_id' => auth()->id(),
            'customer_document' => $factusData['docNum'],
            'customer_name' => $factusData['name'],
            'customer_email' => $factusData['email'],
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $totalAmount,
            'reference_code' => 'POS-TEMP', // temporal, se actualiza en la siguiente línea
            'factus_status' => 'pending',
        ]);

        // Usar el ID real para evitar duplicados en Factus
        $referenceCode = 'POS-'.str_pad($sale->id, 5, '0', STR_PAD_LEFT);
        $sale->update(['reference_code' => $referenceCode]);

        foreach ($cart as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty'],
            ]);

            // Descontar el stock
            if (! empty($item['id'])) {
                $product = Product::find($item['id']);
                if ($product && $product->stock >= $item['qty']) {
                    $product->decrement('stock', $item['qty']);
                }
            }
        }

        // Guardar el cliente en el directorio si no existe
        if (! empty($factusData['docNum']) && ! empty($factusData['name'])) {
            Customer::firstOrCreate(
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
            $responseBody = $e instanceof RequestException
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
