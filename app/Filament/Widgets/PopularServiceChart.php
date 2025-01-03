<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\BookingTransaction;
use App\Models\TransactionDetails;
use App\Models\HomeService;
use Illuminate\Support\Facades\DB;

class PopularServiceChart extends ChartWidget
{
    protected static ?string $heading = 'Top Service';
    protected static ?string $maxHeight = '34vh';
    public function getDescription(): ?string
{
    return 'Top 10 Home Service';
}

    protected function getData(): array
    {
        // Query untuk mendapatkan data dari TransactionDetails yang berhubungan dengan BookingTransaction yang is_paid == true
        $popularServices = TransactionDetails::select('home_service_id', DB::raw('count(*) as total'))
            ->whereHas('bookingTransaction', function ($query) {
                $query->where('is_paid', true);
            })
            ->groupBy('home_service_id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        // Ambil nama dari home service tersebut
        $labels = [];
        $data = [];
        $backgroundColors = [];
        $maxTotal = $popularServices->max('total');
        foreach ($popularServices as $service) {
            $homeService = HomeService::find($service->home_service_id);
            $labels[] = $homeService->name;
            $data[] = $service->total;
            $opacity = $service->total / $maxTotal;
            $backgroundColors[] = "rgba(255, 76, 28, $opacity)";
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Popular Home Services',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
