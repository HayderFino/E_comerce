<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Sale;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información del Cliente')
                    ->schema([
                        TextEntry::make('customer_name')->label('Nombre'),
                        TextEntry::make('customer_document')->label('Documento'),
                        TextEntry::make('customer_email')->label('Email'),
                    ])->columns(3),

                Section::make('Detalles de la Venta')
                    ->schema([
                        TextEntry::make('reference_code')->label('Referencia'),
                        TextEntry::make('created_at')->label('Fecha')->dateTime(),
                        TextEntry::make('subtotal')->label('Subtotal')->money('COP'),
                        TextEntry::make('tax')->label('Impuestos')->money('COP'),
                        TextEntry::make('total')->label('Total')->money('COP')->weight('bold'),
                        TextEntry::make('factus_status')->label('Estado Factus')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'success' => 'success',
                                'failed' => 'danger',
                                default => 'warning',
                            }),
                    ])->columns(3),

                Section::make('Respuesta de Factus (API)')
                    ->schema([
                        TextEntry::make('factus_response')
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
            ->defaultSort('created_at', 'desc')
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
