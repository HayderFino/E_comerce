<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Sale;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Historial de Ventas';

    protected static ?string $pluralModelLabel = 'Ventas';

    protected static ?int $navigationSort = 2;

    public static function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Información del Cliente')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('customer_name')->label('Nombre'),
                        \Filament\Infolists\Components\TextEntry::make('customer_document')->label('Documento'),
                        \Filament\Infolists\Components\TextEntry::make('customer_email')->label('Email'),
                    ])->columns(3),

                \Filament\Infolists\Components\Section::make('Detalles de la Venta')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('reference_code')->label('Referencia'),
                        \Filament\Infolists\Components\TextEntry::make('created_at')->label('Fecha')->dateTime(),
                        \Filament\Infolists\Components\TextEntry::make('subtotal')->label('Subtotal')->money('COP'),
                        \Filament\Infolists\Components\TextEntry::make('tax')->label('Impuestos')->money('COP'),
                        \Filament\Infolists\Components\TextEntry::make('total')->label('Total')->money('COP')->weight('bold'),
                        \Filament\Infolists\Components\TextEntry::make('factus_status')->label('Estado Factus')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'success' => 'success',
                                'failed' => 'danger',
                                default => 'warning',
                            }),
                    ])->columns(3),

                \Filament\Infolists\Components\Section::make('Respuesta de Factus (API)')
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('factus_response')
                            ->label('')
                            ->formatStateUsing(fn ($state) => '<pre style="max-height: 250px; overflow-y: auto;" class="text-xs">'.e(json_encode(json_decode($state), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: $state).'</pre>')
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Read-only info
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_code')
                    ->label('Ref.')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Empleado')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('COP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('factus_status')
                    ->label('Estado Factus')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        default => 'warning',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Ver Factura PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(function (Sale $record) {
                        $data = json_decode($record->factus_response, true);

                        return $data['data']['links']['public_url'] ?? '#';
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (Sale $record): bool => $record->factus_status === 'success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
