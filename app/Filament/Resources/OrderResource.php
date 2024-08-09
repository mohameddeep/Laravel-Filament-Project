<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\RelationManagers\AddressRelationManager;
use App\Models\Order;
use App\Models\Product;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make("order information")->schema([
                        Select::make("user_id")
                        ->label("User")
                        ->relationship("user","name")
                        ->searchable()
                        ->preload()
                        ->required(),

                        Select::make("payment_method")->options([
                            "stripe" =>"Stripe",
                            "cod" =>"Cashe On Delivery",
                        ])
                        ->required(),

                        Select::make("payment_status")->options([
                            "pending" =>"Pending",
                            "paid" =>"Paid",
                            "failed" =>"Failed",
                        ])
                        ->default("pending")
                        ->required(),

                        ToggleButtons::make("status")
                        ->inline()
                        ->default("new")
                        ->required()
                        ->options([
                            "new" =>"New",
                            "processing" =>"Processing",
                            "shipped" =>"Shipped",
                            "delivered" =>"Delivered",
                            "canceled" =>"Canceled",
                        ])->colors([
                            "new" =>"info",
                            "processing" =>"warning",
                            "shipped" =>"success",
                            "delivered" =>"success",
                            "canceled" =>"danger",
                        ])->icons([
                            "new" =>"heroicon-m-sparkles",
                            "processing" =>"heroicon-m-arrow-path",
                            "shipped" =>"heroicon-m-truck",
                            "delivered" =>"heroicon-m-check-badge",
                            "canceled" =>"heroicon-m-x-circle",
                        ]),
                        Select::make("currency")->options([
                            "usd" =>"USD",
                            "eur" =>"EUR",
                            "egp" =>"EGP",
                        ])
                        ->default("egp")
                        ->required(),


                        Select::make("shipping_method")->options([
                            "fedex" =>"FedEx",
                            "ups" =>"UPS",
                            "dhl" =>"DHL",
                        ]),

                        Textarea::make("notes")
                        ->columnSpanFull(),

                Section::make("order_items")->schema([
                    Repeater::make("items")->relationship()
                    ->schema([
                        Select::make("product_id")
                        ->relationship("product","name")
                        ->preload()
                        ->searchable()
                        ->required()
                        ->distinct()
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->columnSpan(4)
                        ->afterStateUpdated(fn ($state,Set $set) =>$set("unit_amount",Product::find($state)?->price ?? 0))
                        ->afterStateUpdated(fn ($state,Set $set) =>$set("total_amount",Product::find($state)?->price ?? 0)),


                    TextInput::make("quantity")
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->columnSpan(2)
                    ->reactive()
                    ->afterStateUpdated(fn ($state,Set $set,Get $get) => $set("total_amount",$state * $get("unit_amount"))),



                    TextInput::make("unit_amount")
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->columnSpan(3),


                    TextInput::make("total_amount")
                      ->numeric()
                    ->required()
                    ->dehydrated()
                    ->columnSpan(3),
                ])->columns(12),


                Placeholder::make("grand_total_placeholder")
                ->label("Grand_Total")
                ->content(function(Get $get,Set $set){
                    $total=0;

                    if(!$repeaters=$get("items")){
                        return $total;
                    }
                    foreach($repeaters as $key=>$repeater){
                        $total+=$get("items.{$key}.total_amount");
                    }
                    $set("grand_total",$total);
                    return Number::currency($total,"EGP");

                }),
                Hidden::make("grand_total")
                ->default(0)


                    ])

                    ])->columns(2)
                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
            AddressRelationManager::class
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 10 ? "sucess" : "danger";
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
