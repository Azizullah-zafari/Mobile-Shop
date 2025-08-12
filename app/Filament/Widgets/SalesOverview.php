<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Order;

class SalesOverview extends Widget
{
    protected static string $view = 'filament.widgets.sales-overview';

    public $totalSalesToday;

    public function mount()
    {
        $this->totalSalesToday = Order::whereDate('order_date', now())
            ->where('status', 'completed')
            ->sum('total_amount');
    }
}
