<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class OrderState extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make("New Order",Order::query()->where("status","new")->count()),
            Stat::make("processing Order",Order::query()->where("status","processing")->count()),
            Stat::make("Shipped Order",Order::query()->where("status","shipped")->count()),
            Stat::make("Delivered Order",Order::query()->where("status","delivered")->count()),
            Stat::make("Canceled Order",Order::query()->where("status","canceled")->count()),
            Stat::make("Average Price",Number::currency(Order::query()->avg("grand_total"),"EGP")),
        ];
    }
}
