<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Punto de Venta - E-commerce</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('favicon.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 font-sans antialiased text-gray-900 h-screen flex flex-col overflow-hidden">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200 py-3 shrink-0">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-bold text-indigo-600">Punto de Venta (POS)</h1>
                <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-1 rounded-full font-medium">Modo Empleado</span>
            </div>
            <div class="flex items-center gap-4">
                <!-- Live Clock -->
                <div x-data="{ time: new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) }" 
                     x-init="setInterval(() => time = new Date().toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' }), 1000)" 
                     class="hidden sm:flex items-center gap-2 px-3 py-1 bg-gray-100 rounded-lg text-gray-600 border border-gray-200">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold font-mono tracking-wide" x-text="time"></span>
                </div>

                @if(auth()->user()->hasRole('Super Admin'))
                    <a href="/admin" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-md text-sm font-medium border border-indigo-700 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Panel de Administración
                    </a>
                @endif
                <a href="{{ route('inventory.index') }}" class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md text-sm font-medium border border-indigo-200 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Ver Inventario
                </a>
                
                <div class="flex items-center gap-3 ml-2 border-l border-gray-200 pl-4">
                    <span class="text-sm font-bold text-gray-700">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-red-600 text-sm font-medium flex items-center gap-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- POS Workspace -->
    <div x-data="posSystem()" class="flex-1 flex overflow-hidden">
        
        <!-- Products Grid (Left Side) -->
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-extrabold text-gray-900">Catálogo</h2>
                <div class="relative">
                    <input type="text" placeholder="Buscar producto..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm w-64">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-20 bg-white rounded-lg shadow-sm border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay productos disponibles</h3>
                </div>
            @else
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm transition flex flex-col h-full {{ $product->stock > 0 ? 'hover:shadow-md cursor-pointer' : 'opacity-60 cursor-not-allowed' }}"
                             @click="{{ $product->stock > 0 ? 'addToCart('.$product->id.', \''.$product->name.'\', '.$product->price.', '.$product->stock.', '.$product->tax_rate.')' : '' }}">
                            <div class="h-32 w-full bg-indigo-50 flex flex-col items-center justify-center rounded-t-xl border-b border-gray-100 relative">
                                @if($product->stock <= 0)
                                    <div class="absolute inset-0 bg-white/50 z-10 flex items-center justify-center">
                                        <span class="bg-red-100 text-red-600 font-bold px-3 py-1 rounded-full text-xs shadow-sm border border-red-200">AGOTADO</span>
                                    </div>
                                @endif
                                @if($product->image_url)
                                    <img class="h-full w-full object-cover rounded-t-xl" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                @else
                                    <svg class="w-10 h-10 text-indigo-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    <span class="text-indigo-400 text-xs font-medium">Prod. #{{ $product->id }}</span>
                                @endif
                            </div>
                            <div class="p-4 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 line-clamp-2 leading-tight">{{ $product->name }}</h3>
                                    <p class="text-xs {{ $product->stock > 0 ? 'text-green-600' : 'text-red-500' }} mt-1 font-medium">
                                        Stock: {{ $product->stock }} disponibles
                                    </p>
                                </div>
                                <div class="mt-3">
                                    <span class="text-lg font-extrabold text-gray-900">${{ number_format($product->price, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>

        <!-- Cart and Factus Checkout (Right Side) -->
        <aside class="w-96 bg-white border-l border-gray-200 flex flex-col shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.05)] z-10 shrink-0">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800 flex justify-between items-center">
                    <span>Orden Actual</span>
                    <span class="bg-indigo-600 text-white text-xs px-2 py-1 rounded-full" x-text="cart.length + ' items'"></span>
                </h3>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-white">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="text-sm">El carrito está vacío</p>
                    </div>
                </template>

                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex justify-between items-start border-b border-gray-100 pb-3">
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800" x-text="item.name"></h4>
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                Max: <span x-text="item.maxStock"></span> &bull; 
                                <span x-text="item.taxRate == 0 ? 'Excluido de IVA' : 'IVA: ' + item.taxRate + '%'"></span>
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <button @click="updateQty(index, -1)" class="w-6 h-6 rounded-md bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200">-</button>
                                <span class="text-sm font-medium w-4 text-center" x-text="item.qty"></span>
                                <button @click="updateQty(index, 1)" :disabled="item.qty >= item.maxStock" class="w-6 h-6 rounded-md bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200 disabled:opacity-30 disabled:cursor-not-allowed">+</button>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <span class="text-sm font-bold text-gray-900" x-text="'$' + formatMoney(item.price * item.qty)"></span>
                            <button @click="removeFromCart(index)" class="block text-xs text-red-500 hover:text-red-700 mt-1 text-right w-full">Quitar</button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Customer & Factus Info -->
            <div class="p-4 border-t border-gray-200 bg-gray-50 flex-col gap-3" x-show="cart.length > 0">
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Datos Facturación (Factus)</h4>
                
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <select x-model="factus.docType" class="text-sm border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-2">
                        <option value="13">CC</option>
                        <option value="31">NIT</option>
                        <option value="22">CE</option>
                    </select>
                    <input type="text" x-model="factus.docNum" @input.debounce.500ms="searchCustomer" placeholder="Número Doc." class="text-sm border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-2">
                </div>
                <input type="text" x-model="factus.name" placeholder="Nombre / Razón Social" class="w-full text-sm border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-2 mb-2">
                
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <input type="email" x-model="factus.email" placeholder="Correo electrónico" class="w-full text-sm border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-2">
                    <input type="tel" x-model="factus.phone" placeholder="Teléfono" class="w-full text-sm border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-2">
                </div>
                <input type="text" x-model="factus.address" placeholder="Dirección" class="w-full text-sm border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-2 mb-3">
                
                <div class="grid grid-cols-2 gap-2 mb-2">
                    <select x-model="factus.paymentMethod" class="text-sm border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-2">
                        <option value="10">Efectivo</option>
                        <option value="48">Tarjeta Crédito</option>
                        <option value="49">Tarjeta Débito</option>
                    </select>
                    <select x-model="factus.paymentForm" class="text-sm border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5 px-2">
                        <option value="1">Contado</option>
                        <option value="2">Crédito</option>
                    </select>
                </div>
            </div>

            <!-- Totals & Pay -->
            <div class="p-4 border-t border-gray-200 bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <div class="flex justify-between text-gray-600 mb-1">
                    <span class="text-sm">Subtotal</span>
                    <span class="text-sm font-medium" x-text="'$' + formatMoney(subtotal)"></span>
                </div>
                <div class="flex justify-between text-gray-600 mb-3">
                    <span class="text-sm">Impuestos (IVA)</span>
                    <span class="text-sm font-medium" x-text="'$' + formatMoney(tax)"></span>
                </div>
                <div class="flex justify-between items-center mb-4 pt-2 border-t border-gray-200">
                    <span class="text-lg font-bold text-gray-900">Total</span>
                    <span class="text-2xl font-black text-indigo-600" x-text="'$' + formatMoney(total)"></span>
                </div>
                
                <button @click="processSale()" :disabled="cart.length === 0 || isProcessing" 
                        class="w-full bg-indigo-600 text-white font-bold text-lg py-3 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-md flex justify-center items-center">
                    <span x-show="!isProcessing">Facturar y Cobrar</span>
                    <span x-show="isProcessing" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Procesando...
                    </span>
                </button>
            </div>
        </aside>
    </div>

    <script>
        function posSystem() {
            return {
                cart: [],
                isProcessing: false,
                factus: {
                    docType: '13',
                    docNum: '',
                    name: '',
                    email: '',
                    phone: '',
                    address: '',
                    paymentMethod: '10',
                    paymentForm: '1'
                },
                async searchCustomer() {
                    if (this.factus.docNum.length < 4) return;
                    try {
                        const res = await fetch(`/api/customers/${this.factus.docNum}`);
                        const data = await res.json();
                        if (data.success) {
                            this.factus.docType = data.customer.document_type;
                            this.factus.name = data.customer.name;
                            this.factus.email = data.customer.email || '';
                            this.factus.phone = data.customer.phone || '';
                            this.factus.address = data.customer.address || '';
                        }
                    } catch (e) {
                        console.error('Error buscando cliente', e);
                    }
                },
                addToCart(id, name, price, maxStock, taxRate) {
                    if (maxStock <= 0) return; // Prevent out of stock clicks
                    
                    const existing = this.cart.find(i => i.id === id);
                    if (existing) {
                        if (existing.qty < maxStock) {
                            existing.qty++;
                        } else {
                            alert('No hay más stock disponible para este producto.');
                        }
                    } else {
                        const tr = taxRate !== undefined && taxRate !== null ? parseFloat(taxRate) : 19;
                        this.cart.push({ id, name, price: parseFloat(price), qty: 1, maxStock: parseInt(maxStock), taxRate: tr });
                    }
                },
                updateQty(index, change) {
                    const item = this.cart[index];
                    const newQty = item.qty + change;
                    
                    if (newQty > item.maxStock) {
                        return; // Blocks exceeding max stock via + button
                    }
                    
                    item.qty = newQty;
                    
                    if (item.qty <= 0) {
                        this.cart.splice(index, 1);
                    }
                },
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                get tax() {
                    return this.cart.reduce((sum, item) => {
                        return sum + ((item.price * item.qty) * (item.taxRate / 100));
                    }, 0);
                },
                get total() {
                    return this.subtotal + this.tax;
                },
                formatMoney(amount) {
                    return Math.round(amount).toLocaleString('es-CO');
                },
                async processSale() {
                    if(!this.factus.docNum || !this.factus.name || !this.factus.email) {
                        alert('Por favor complete los datos de facturación (Documento, Nombre, Correo).');
                        return;
                    }
                    
                    if(this.isProcessing) return; // Doble validación por seguridad
                    
                    this.isProcessing = true; // Bloquea el botón
                    
                    const payload = {
                        cart: this.cart,
                        factus_data: this.factus,
                        totals: {
                            subtotal: this.subtotal,
                            tax: this.tax,
                            total: this.total
                        }
                    };
                    
                    console.log('Procesando Venta para Factus...', payload);
                    
                    try {
                        const response = await fetch('/sales/process', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify(payload)
                        });
                        
                        const data = await response.json();
                        
                        if(response.ok && data.success) {
                            alert('¡Venta Exitosa!\n' + data.message);
                            console.log('Respuesta Factus:', data);
                            // Reset
                            this.cart = [];
                            this.factus.docNum = '';
                            this.factus.name = '';
                            this.factus.email = '';
                            this.factus.phone = '';
                            this.factus.address = '';
                        } else {
                            alert('Hubo un error al procesar la factura.\nRevisa la consola para más detalles.');
                            console.error('Error desde el servidor:', data);
                        }
                    } catch (error) {
                        alert('Error de conexión con el servidor.');
                        console.error(error);
                    } finally {
                        this.isProcessing = false; // Desbloquea el botón siempre, incluso si falla
                    }
                }
            }
        }
    </script>
    <!-- AI Assistant Widget (OCULTO POR AHORA PARA IMPLEMENTAR MÁS ADELANTE) -->
    <div x-data="aiAssistant()" class="fixed bottom-6 right-6 z-50 hidden">
        <!-- Chat Button -->
        <button @click="open = !open" class="bg-indigo-600 text-white p-4 rounded-full shadow-lg hover:bg-indigo-700 transition flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        </button>

        <!-- Chat Window -->
        <div x-show="open" x-transition class="absolute bottom-16 right-0 w-80 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col" style="height: 400px; display: none;">
            <div class="bg-indigo-600 text-white p-3 font-bold flex justify-between items-center">
                <span>🤖 Asistente IA</span>
                <button @click="open = false" class="text-white hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="flex-1 p-3 overflow-y-auto bg-gray-50 flex flex-col gap-2" id="chat-messages">
                <template x-for="msg in messages">
                    <div :class="msg.role === 'user' ? 'text-right' : 'text-left'">
                        <span :class="msg.role === 'user' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-200 text-gray-800'" class="inline-block p-2 rounded-lg text-sm max-w-[80%]" x-text="msg.text"></span>
                    </div>
                </template>
                <div x-show="loading" class="text-left">
                    <span class="bg-gray-200 text-gray-800 inline-block p-2 rounded-lg text-sm shadow-sm font-medium animate-pulse">Pensando...</span>
                </div>
            </div>

            <div class="p-3 border-t bg-white flex gap-2">
                <input type="text" x-model="input" @keydown.enter="sendMessage" placeholder="Pregunta algo..." class="flex-1 text-sm rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                <button @click="sendMessage" :disabled="loading" class="bg-indigo-600 text-white p-2 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        function aiAssistant() {
            return {
                open: false,
                input: '',
                loading: false,
                messages: [
                    { role: 'ai', text: '¡Hola! Soy Gemini. Conozco el inventario actual y tus ventas de hoy. ¿En qué te ayudo?' }
                ],
                sendMessage() {
                    if (this.input.trim() === '') return;
                    
                    const userMsg = this.input;
                    this.messages.push({ role: 'user', text: userMsg });
                    this.input = '';
                    this.loading = true;

                    this.$nextTick(() => {
                        const box = document.getElementById('chat-messages');
                        box.scrollTop = box.scrollHeight;
                    });

                    fetch('/ai/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ message: userMsg })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.messages.push({ role: 'ai', text: data.reply });
                    })
                    .catch(() => {
                        this.messages.push({ role: 'ai', text: 'Error de conexión con Gemini.' });
                    })
                    .finally(() => {
                        this.loading = false;
                        this.$nextTick(() => {
                            const box = document.getElementById('chat-messages');
                            box.scrollTop = box.scrollHeight;
                        });
                    });
                }
            }
        }
    </script>
</body>
</html>
