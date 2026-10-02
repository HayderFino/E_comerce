<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Ventas Hoy', Sale::whereDate('created_at', today())->count())
                ->description('Total de transacciones de hoy'),
            Stat::make('Ingresos Hoy', '$'.number_format(Sale::whereDate('created_at', today())->sum('total'), 0))
                ->description('Total facturado hoy')
                ->color('success'),
            Stat::make('Facturas Exitosas (Factus)', Sale::where('factus_status', 'success')->count())
                ->description('Enviadas a la DIAN')
                ->color('primary'),
        ];
    }
}
