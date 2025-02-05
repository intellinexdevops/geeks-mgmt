<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Forms\Components\Builder;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // public function getTabs(): array
    // {
    //     return [
    //         'all' => Tab::make('All Users'),
    //         'active' => Tab::make('Active')
    //             ->modifyQueryUsing(fn(Builder $query) => $query->where('active', true)),
    //         'inactive' => Tab::make('Inactive')
    //             ->modifyQueryUsing(fn(Builder $query) => $query->where('active', false)),
    //     ];
    // }
}
