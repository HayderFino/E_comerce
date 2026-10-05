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
