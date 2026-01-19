<?php

namespace App\Filament\Resources\Sales;

use App\Filament\Resources\Sales\Pages\CreateSale;
use App\Filament\Resources\Sales\Pages\EditSale;
use App\Filament\Resources\Sales\Pages\ListSales;
use App\Filament\Resources\Sales\Schemas\SaleForm;
use App\Filament\Resources\Sales\Tables\SalesTable;
use App\Models\Sale;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Navigation\NavigationItem;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?int $navigationSort = 20;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    protected static ?string $navigationLabel = 'Daftar Penjualan';

    protected static string | UnitEnum | null $navigationGroup = 'Bill';

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Daftar Penjualan')
                ->group(static::$navigationGroup)
                ->icon('heroicon-o-list-bullet')
                ->sort(static::$navigationSort)
                ->url(static::getUrl('index')),
            NavigationItem::make('Tambah Penjualan')
                ->group(static::$navigationGroup)
                ->icon('heroicon-o-plus-circle')
                ->sort(static::$navigationSort + 1)
                ->url(static::getUrl('create')),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return SaleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesTable::configure($table);
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
            'index' => ListSales::route('/'),
            'create' => CreateSale::route('/create'),
            'edit' => EditSale::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->roles->whereIn('name', ['admin', 'owner'])->isNotEmpty() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->roles->whereIn('name', ['admin', 'owner'])->isNotEmpty() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->roles->whereIn('name', ['admin', 'owner'])->isNotEmpty() ?? false;
    }
}
