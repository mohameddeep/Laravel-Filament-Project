<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
class LatestOrders extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;
    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort("created_at","desc")
            ->columns([
                TextColumn::make("user.name")
                ->label("customer")
                ->sortable()
                ->searchable(),


                TextColumn::make("grand_total")
                ->label("grand total")
                ->numeric()
                ->sortable()
                ->money("EGP"),

                TextColumn::make("payment_method")
                ->sortable()
                ->searchable(),

                TextColumn::make("payment_status")
                ->sortable()
                ->searchable(),

                TextColumn::make("currency")
                ->sortable()
                ->searchable(),


                TextColumn::make("shipping_method")
                ->sortable()
                ->searchable(),


                SelectColumn::make("status")
                ->options([
                    "new" =>"New",
                    "processing" =>"Processing",
                    "shipped" =>"Shipped",
                    "delivered" =>"Delivered",
                    "canceled" =>"Canceled",
                ])
                ->sortable()
                ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),

            ]);
    }
}
