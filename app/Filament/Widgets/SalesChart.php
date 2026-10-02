<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Ventas de los últimos 7 días';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Sale::selectRaw('DATE(created_at) as date, SUM(total) as aggregate')
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->isoFormat('D MMM');

            $sale = $data->firstWhere('date', $date);
            $values[] = $sale ? $sale->aggregate : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Ventas Totales ($)',
                    'data' => $values,
                    'borderColor' => '#4f46e5', // Indigo-600
                    'backgroundColor' => 'rgba(79, 70, 229, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
