<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - E-commerce</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900" x-data="inventoryManager()">

    <!-- Top Navigation -->
    <nav class="bg-indigo-600 shadow-md h-14 flex items-center justify-between px-6 z-20 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-indigo-600 font-black text-xl">
                E
            </div>
            <h1 class="text-white font-bold text-lg tracking-wide">E-commerce <span class="font-normal opacity-80 text-sm">| Panel Administrativo</span></h1>
        </div>
        <div class="flex items-center gap-4">
            <!-- Live Clock -->
            <div x-data="{ time: new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }" 
                 x-init="setInterval(() => time = new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)" 
                 class="hidden sm:flex items-center gap-2 px-3 py-1 bg-indigo-700/50 rounded-lg text-indigo-50 border border-indigo-500">
                <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-bold font-mono tracking-wide" x-text="time"></span>
            </div>

            <a href="{{ route('home') }}" class="text-indigo-100 hover:text-white transition text-sm font-medium flex items-center gap-1 bg-indigo-700/50 px-3 py-1.5 rounded-lg hover:bg-indigo-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Ir al POS
            </a>
            <div class="flex items-center gap-2 text-indigo-100">
                <div class="w-7 h-7 rounded-full bg-indigo-500 flex items-center justify-center border border-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <span class="text-sm font-medium">Cajero 1</span>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-white flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Inventario de Productos</h2>
                    <p class="text-sm text-gray-500 mt-1">Gestiona el stock disponible y los precios del catálogo.</p>
                </div>
                <button @click="openModal('create')" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Nuevo Producto
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                            <th class="px-6 py-4 font-medium">ID</th>
                            <th class="px-6 py-4 font-medium">Producto</th>
                            <th class="px-6 py-4 font-medium">Precio (COP)</th>
                            <th class="px-6 py-4 font-medium">Stock</th>
                            <th class="px-6 py-4 font-medium">Estado</th>
                            <th class="px-6 py-4 font-medium text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">#{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 truncate w-64">{{ $product->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                ${{ number_format($product->price, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold {{ $product->stock > 0 ? 'text-gray-900' : 'text-red-600' }}">{{ $product->stock }}</span>
                                <span class="text-xs text-gray-400 ml-1">unidades</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->stock > 5)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Óptimo
                                    </span>
                                @elseif($product->stock > 0 && $product->stock <= 5)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        Bajo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        Agotado
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openModal('edit', {{ $product->toJson() }})" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No hay productos en el inventario. Haz clic en "Nuevo Producto" para empezar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-xs text-gray-500">
                Mostrando {{ count($products) }} productos en el catálogo.
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-transparent border-t border-gray-200 py-6 mt-4">
        <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-gray-500">
            <span>&copy; {{ date('Y') }} Mi Tienda - Panel Administrativo</span>
            <div class="flex items-center gap-4">
                <a href="https://github.com/HayderFino" target="_blank" class="hover:text-indigo-600 transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                    GitHub
                </a>
                <a href="https://www.linkedin.com/in/hayder-andrey-fino-suarez-605564336/?isSelfProfile=true" target="_blank" class="hover:text-indigo-600 transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    LinkedIn
                </a>
            </div>
        </div>
    </footer>

    <!-- Modal Form -->
    <div x-show="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="closeModal()"></div>

            <!-- Modal Panel -->
            <div class="relative z-10 inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <h3 class="text-lg font-bold leading-6 text-gray-900" x-text="modalMode === 'create' ? 'Nuevo Producto' : 'Editar Producto'"></h3>
                    <div class="mt-5">
                        <form :action="modalMode === 'create' ? '{{ route('products.store') }}' : '/products/' + formData.id" method="POST">
                            @csrf
                            <template x-if="modalMode === 'edit'">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                                    <input type="text" name="name" x-model="formData.name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <textarea name="description" x-model="formData.description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Precio (COP)</label>
                                        <input type="number" name="price" x-model="formData.price" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Stock</label>
                                        <input type="number" name="stock" x-model="formData.stock" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">IVA (%)</label>
                                        <select name="tax_rate" x-model="formData.tax_rate" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border">
                                            <option value="19.00">19% (Estándar)</option>
                                            <option value="5.00">5%</option>
                                            <option value="0.00">0%</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center mt-6">
                                        <input type="checkbox" name="is_tax_excluded" x-model="formData.is_tax_excluded" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label class="ml-2 block text-sm text-gray-900">Excluido de IVA (ej. salud)</label>
                                    </div>
                                    <input type="hidden" name="tax_code" value="01">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">URL Imagen (Opcional)</label>
                                    <input type="url" name="image_url" x-model="formData.image_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2 px-3 border">
                                </div>
                            </div>

                            <div class="mt-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                    Guardar
                                </button>
                                <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
    <script>
        function inventoryManager() {
            return {
                isModalOpen: false,
                modalMode: 'create', // create | edit
                formData: {
                    id: '',
                    name: '',
                    description: '',
                    price: '',
                    stock: '',
                    image_url: '',
                    tax_rate: '19.00',
                    is_tax_excluded: false
                },
                openModal(mode, product = null) {
                    this.modalMode = mode;
                    if (mode === 'edit' && product) {
                        this.formData = {
                            id: product.id,
                            name: product.name,
                            description: product.description || '',
                            price: product.price,
                            stock: product.stock,
                            image_url: product.image_url || '',
                            tax_rate: product.tax_rate || '19.00',
                            is_tax_excluded: product.is_tax_excluded || false
                        };
                    } else {
                        this.formData = { id: '', name: '', description: '', price: '', stock: '', image_url: '', tax_rate: '19.00', is_tax_excluded: false };
                    }
                    this.isModalOpen = true;
                },
                closeModal() {
                    this.isModalOpen = false;
                }
            }
        }
    </script>
</body>
</html>
