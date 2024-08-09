<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Support\Str;
use Filament\Forms\Set;
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([


                    Section::make('product information')->schema([

                        TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur:true)
                         ->afterStateUpdated(fn(string $operation,$state,Set $set) =>$operation
                        == "create" ? $set("slug", Str::slug($state)) : null
             ),

                        TextInput::make('slug')
                        ->required()
                        ->disabled()
                        ->maxLength(255)
                        ->unique(Product::class,"slug",ignoreRecord:true)
                        ->dehydrated(),

                        MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->fileAttachmentsDirectory("products"),


                    ])->columns(2),


                    Section::make("images")->schema([
                        FileUpload::make("images")
                        ->multiple()
                        ->directory("products")
                        ->maxFiles(5)
                        ->reorderable()
                    ]),



                ])->columnSpan(2),


                Group::make()->schema([


                    Section::make('price')->schema([
                    TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix("inr"),

                    ]),
                    Section::make('Assisocation')->schema([
                    Select::make('catogry_id')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->relationship("catogry","name"),


                    Select::make('brand_id')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->relationship("brand","name"),

                    ]),



                    Section::make('Staus')->schema([
                        Toggle::make('in_stock')
                        ->required()
                        ->default(true),

                        Toggle::make('is_active')
                        ->required()
                        ->default(true),


                        Toggle::make('is_featured')
                        ->required(),

                        Toggle::make('in_sale')
                        ->required(),

                        ]),

                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('catogry.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('in_sale')
                    ->boolean(),
                Tables\Columns\IconColumn::make('in_stock')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make("catogry")
                ->relationship("catogry","name"),
                SelectFilter::make("brand")
                ->relationship("brand","name"),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
