<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatogryResource\Pages;
use App\Filament\Resources\CatogryResource\RelationManagers;
use App\Models\Catogry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class CatogryResource extends Resource
{
    protected static ?int $navigationSort = 3;
    protected static ?string $model = Catogry::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make([
                    Grid::make()
                    ->schema([
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
                        ->unique(Catogry::class,"slug",ignoreRecord:true)
                        ->dehydrated()
                        ,

                ]),

                FileUpload::make('image')
                    ->image()
                    ->directory("catogries"),

                    Toggle::make('is_active')
                    ->required()
                    ->default(true),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\IconColumn::make('is_active')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatogries::route('/'),
            'create' => Pages\CreateCatogry::route('/create'),
            'edit' => Pages\EditCatogry::route('/{record}/edit'),
        ];
    }
}
