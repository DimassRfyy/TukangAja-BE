<?php
 
namespace App\Filament\Widgets;
 
use Filament\Widgets\ChartWidget;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\DB;
 
class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Transaction Chart';
    protected static ?string $maxHeight = '36vh';
    public function getDescription(): ?string
{
    return 'Total Amount Paid per Month';
}
 
    protected function getData(): array
    {
        $monthlyAmounts = BookingTransaction::select(
                DB::raw('SUM(total_amount) as total'),
                DB::raw('MONTH(created_at) as month')
            )
            ->where('is_paid', true)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total', 'month')
            ->toArray();

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[] = $monthlyAmounts[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Amount Paid',
                    'data' => $data,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
 
    protected function getType(): string
    {
        return 'bar';
    }
}