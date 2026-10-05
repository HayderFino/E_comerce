<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        return view('home', compact('products'));
    }

    public function inventory()
    {
        $products = Product::all();

        return view('inventory.index', compact('products'));
    }

    public function productsGrid()
    {
        $products = Product::where('is_active', true)->get();
        return view('partials.products-grid', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
            'tax_code' => 'required|string|max:5',
            'tax_rate' => 'required|numeric|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']).'-'.time();
        $validated['is_tax_excluded'] = $request->boolean('is_tax_excluded');
        
        Product::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        //
    }

    public function edit(Product $product)
    {
        //
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image_url' => 'nullable|url',
            'tax_code' => 'required|string|max:5',
            'tax_rate' => 'required|numeric|min:0',
        ]);

        $validated['is_tax_excluded'] = $request->boolean('is_tax_excluded');
        $product->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('inventory.index')->with('success', 'Producto eliminado exitosamente.');
    }
}
