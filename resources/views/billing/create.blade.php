<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Factura Electrónica</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900">
    <!-- Navbar -->
    <nav class="bg-indigo-600 shadow-md py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-white">Mi Tienda - Facturación</h1>
            <div class="flex gap-4">
                <a href="/home" class="text-indigo-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Volver a Home</a>
                <a href="/" class="text-indigo-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Salir</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-10">
            <h2 class="text-3xl font-extrabold text-gray-900">Nueva Factura Electrónica</h2>
            <p class="mt-2 text-md text-gray-500">Completa los datos requeridos por la DIAN a través de Factus para generar el comprobante electrónico.</p>
        </div>

        <form action="#" method="POST" class="space-y-8">
            @csrf
            
            <!-- Datos del Cliente -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">1. Datos del Cliente (Adquiriente)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de Documento</label>
                        <select name="identification_document_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="3">Cédula de Ciudadanía (CC)</option>
                            <option value="6">NIT</option>
                            <option value="4">Cédula de Extranjería (CE)</option>
                            <option value="1">Pasaporte</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Número de Identificación</label>
                        <input type="text" name="identification" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="123456789" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombres / Razón Social</label>
                        <input type="text" name="graphic_representation_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Ej. Juan Pérez o Mi Empresa S.A.S" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="cliente@correo.com" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="3001234567">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" name="address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Calle 123 # 45 - 67">
                    </div>
                </div>
            </div>

            <!-- Datos de Facturación -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">2. Condiciones de Pago</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Forma de Pago</label>
                        <select name="payment_form" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="1">Contado</option>
                            <option value="2">Crédito</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Método de Pago</label>
                        <select name="payment_method_code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="10">Efectivo</option>
                            <option value="42">Consignación bancaria</option>
                            <option value="48">Tarjeta de crédito</option>
                            <option value="49">Tarjeta de débito</option>
                            <option value="47">Transferencia débito bancario</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                        <input type="date" name="payment_due_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                        <textarea name="observation" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Notas adicionales a la factura..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Productos / Items -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="text-lg font-semibold text-gray-800">3. Detalles de Productos</h3>
                    <button type="button" class="text-sm bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 rounded-md font-medium transition">
                        + Agregar Producto
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cant.</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Unit.</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Impuesto (%)</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="text" name="items[0][code_reference]" value="PROD-001" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-center">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="text" name="items[0][name]" value="Producto de Prueba" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap w-24">
                                    <input type="number" name="items[0][quantity]" value="1" min="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-center">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap w-32">
                                    <input type="number" name="items[0][price]" value="10000" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-right">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap w-28">
                                    <select name="items[0][tax_rate]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-right">
                                        <option value="19.00">19% (IVA)</option>
                                        <option value="5.00">5% (IVA)</option>
                                        <option value="0.00">0%</option>
                                    </select>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right font-medium text-gray-900">
                                    $ 11,900
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button type="button" class="text-red-500 hover:text-red-700">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="mt-6 flex justify-end">
                    <div class="w-1/3 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between py-1">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">$ 10,000</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-gray-600">Impuestos</span>
                            <span class="font-medium">$ 1,900</span>
                        </div>
                        <div class="flex justify-between py-2 mt-2 border-t border-gray-300">
                            <span class="text-lg font-bold text-gray-900">Total a Pagar</span>
                            <span class="text-lg font-bold text-indigo-600">$ 11,900</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <button type="submit" class="inline-flex justify-center py-3 px-8 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out transform hover:-translate-y-1">
                    Emitir Factura Electrónica
                </button>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-400 text-sm">
            &copy; {{ date('Y') }} Mi Tienda - Integración Factus. Todos los derechos reservados.
        </div>
    </footer>
</body>
</html>
