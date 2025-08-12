<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $totalSales = Order::sum('total_amount');
        $totalOrders = Order::count();
        $totalProfit = OrderItem::sum(DB::raw('(unit_price - cost_price) * quantity'));

        return [
            Card::make('کل فروش', number_format($totalSales) . ' AFN'),
            Card::make('تعداد سفارشات', $totalOrders),
            Card::make('کل سود', number_format($totalProfit) . ' AFN'),
        ];
    }
}
