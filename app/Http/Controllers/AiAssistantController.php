<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiAssistantController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = $request->message;
        $apiKey = env('GEMINI_API_KEY');

        if (! $apiKey || $apiKey === 'TU_API_KEY_COMPLETA_AQUI') {
            return response()->json([
                'reply' => 'Por favor, configura tu GEMINI_API_KEY en el archivo .env para que pueda ayudarte.',
            ]);
        }

        // Contexto: Buscar inventario y ventas de hoy
        $inventory = Product::select('name', 'price', 'stock')->get();
        $salesToday = Sale::whereDate('created_at', today())
            ->where('user_id', auth()->id())
            ->get();
        $totalSales = $salesToday->sum('total');
        $countSales = $salesToday->count();

        // Armar el System Prompt
        $systemPrompt = "Eres un asistente de Inteligencia Artificial integrado en el Punto de Venta (POS) de nuestra tienda. Tu trabajo es responder de forma amable, corta y precisa a las dudas del cajero basándote ÚNICAMENTE en la siguiente información de la tienda:\n\n";
        $systemPrompt .= "INVENTARIO ACTUAL:\n".$inventory->toJson()."\n\n";
        $systemPrompt .= "VENTAS DEL CAJERO HOY:\nCantidad de ventas: $countSales\nDinero total vendido hoy: $".number_format($totalSales, 0)." COP.\n\n";
        $systemPrompt .= "Reglas:\n- Si te preguntan por un producto que no está en el inventario, di que no lo tenemos.\n- Sé amigable pero directo.\n- El usuario que te habla es el cajero (empleado).\n";

        // Llamada a la API de Gemini
        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.8-flash:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $systemPrompt."\nPregunta del cajero: ".$userMessage],
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No entendí la respuesta de Gemini.';

            return response()->json(['reply' => $reply]);
        }

        return response()->json([
            'reply' => 'Hubo un error comunicándose con Gemini: '.$response->body(),
        ], 500);
    }
}
