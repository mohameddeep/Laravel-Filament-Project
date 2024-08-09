<?php

namespace App\Filament\Resources\CatogryResource\Pages;

use App\Filament\Resources\CatogryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCatogries extends ListRecords
{
    protected static string $resource = CatogryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
