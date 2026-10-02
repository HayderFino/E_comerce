<?php

namespace App\Services;

use App\Models\Sale;
use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FactusService
{
    protected string $baseUrl;

    protected string $email;

    protected string $password;

    protected string $clientId;

    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.factus.base_url');
        $this->email = config('services.factus.email');
        $this->password = config('services.factus.password');
        $this->clientId = config('services.factus.client_id');
        $this->clientSecret = config('services.factus.client_secret');
    }

    /**
     * Get a valid access token from cache or request a new one.
     */
    public function getToken(): string
    {
        return Cache::remember('factus_access_token', now()->addMinutes(50), function () {
            $response = Http::asForm()->post("{$this->baseUrl}/oauth/token", [
                'grant_type' => 'password',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'username' => $this->email,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Store the refresh token as well, could be useful later
                if (isset($data['refresh_token'])) {
                    Cache::put('factus_refresh_token', $data['refresh_token'], now()->addDays(30));
                }

                return $data['access_token'];
            }

            Log::error('Factus Authentication Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new Exception('No se pudo obtener el token de autenticación de Factus API.');
        });
    }

    /**
     * Get the configured HTTP client with Auth and Rate Limit Handling
     */
    protected function client(): PendingRequest
    {
        return Http::withToken($this->getToken())
            ->acceptJson()
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->retry(3, function (int $attempt, Exception $exception) {
                // If it's an HTTP exception, check for Rate Limit (429)
                if ($exception instanceof RequestException && $exception->response->status() === 429) {
                    $response = $exception->response;
                    $retryAfter = $response->header('Retry-After');
                    $reset = $response->header('X-RateLimit-Reset');

                    $waitTime = 1000; // default 1s
                    if ($retryAfter) {
                        $waitTime = ((int) $retryAfter) * 1000;
                    } elseif ($reset) {
                        $waitTime = max(1000, ((int) $reset - time()) * 1000);
                    } else {
                        $waitTime = $attempt * 2000; // exponential backoff fallback
                    }

                    Log::warning("Factus API Rate Limit reached. Retrying after {$waitTime}ms...");

                    return $waitTime;
                }

                // For other errors, don't retry by default
                return 0;
            });
    }

    /**
     * Valida una factura en la API de Factus.
     */
    public function validateInvoice(array $invoiceData): array
    {
        $response = $this->client()->post("{$this->baseUrl}/v2/bills/validate", $invoiceData);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Factus Validate Invoice Failed', [
            'status' => $response->status(),
            'body' => $response->body(),
            'payload' => $invoiceData,
        ]);

        $response->throw();

        return [];
    }

    /**
     * Obtener los rangos de facturación disponibles.
     */
    public function getNumberingRanges(): array
    {
        $response = $this->client()->get("{$this->baseUrl}/v2/numbering-ranges");

        if ($response->successful()) {
            return $response->json();
        }

        $response->throw();

        return [];
    }

    /**
     * Map a Sale model to Factus payload and validate the invoice.
     */
    public function createInvoiceFromSale(Sale $sale, array $factusData = []): array
    {
        $items = [];
        foreach ($sale->items as $item) {
            $product = $item->product;
            $taxRate = $product ? $product->tax_rate : 19.00;
            $taxCode = $product ? $product->tax_code : '01';
            $isExcluded = $product ? $product->is_tax_excluded : false;

            $taxes = [];
            if ($isExcluded) {
                $taxes[] = [
                    'code' => $taxCode,
                    'is_excluded' => 1,
                ];
            } else {
                $taxes[] = [
                    'code' => $taxCode,
                    'rate' => number_format($taxRate, 2, '.', ''),
                    'is_fixed_value' => 0,
                ];
            }

            $items[] = [
                'code_reference' => 'PROD-'.str_pad($item->product_id ?? $item->id, 4, '0', STR_PAD_LEFT),
                'name' => $item->product_name,
                'quantity' => $item->quantity,
                'discount_rate' => 0.00,
                'price' => $item->price,
                'unit_measure_code' => '94', // 94 = Unidad
                'standard_code' => '999',
                'taxes' => $taxes,
            ];
        }

        $payload = [
            'reference_code' => $sale->reference_code.'-'.now()->format('YmdHis'),
            'document' => '01', // 01 = Factura Electrónica
            'numbering_range_id' => env('FACTUS_NUMBERING_RANGE_ID', 389),
            'operation_type' => '10', // 10 = Estándar
            'send_email' => true,
            'observation' => 'Venta generada desde POS - '.$sale->reference_code,
            'payment_details' => [
                [
                    'payment_form' => $factusData['paymentForm'] ?? '1',
                    'payment_method_code' => $factusData['paymentMethod'] ?? '10',
                    'amount' => number_format($sale->total, 2, '.', ''),
                ],
            ],
            'customer' => [
                'identification_document_code' => $factusData['docType'] ?? '13', // 13 = CC, 31 = NIT
                'identification' => $sale->customer_document ?: '22222222222',
                'legal_organization_code' => '2', // Persona natural
                'names' => $sale->customer_name ?: 'Consumidor Final',
                'address' => ! empty($factusData['address']) ? $factusData['address'] : 'No registrada',
                'email' => $sale->customer_email ?: 'cliente@ejemplo.com',
                'phone' => $factusData['phone'] ?? '0000000',
                'municipality_code' => '68020', // Albania, Santander
                'tribute_code' => 'ZZ',
            ],
            'items' => $items,
        ];

        return $this->validateInvoice($payload);
    }
}
