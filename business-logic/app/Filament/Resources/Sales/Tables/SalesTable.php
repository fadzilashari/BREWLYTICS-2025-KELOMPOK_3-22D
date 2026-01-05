<?php

namespace App\Filament\Resources\Sales\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sale_date')
                    ->label('Tanggal Penjualan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Kasir / Admin')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('item_product')
                    ->label('Nama Product')
                    ->html()
                    ->state(function (\App\Models\Sale $record) {
                        $list = [];
                        foreach ($record->salesItems as $item) {
                            $name = $item->product ? $item->product->name : 'Unknown';
                            $list[] = "<div class='py-1'>{$name}</div>";
                        }
                        return implode('', $list);
                    })
                    ->searchable(query: function ($query, $search) {
                         $query->whereHas('salesItems.product', function ($q) use ($search) {
                             $q->where('name', 'like', "%{$search}%");
                         });
                    }),

                TextColumn::make('item_qty')
                    ->label('Jumlah Item')
                    ->html()
                    ->alignCenter()
                    ->state(function (\App\Models\Sale $record) {
                        $list = [];
                        foreach ($record->salesItems as $item) {
                            $list[] = "<div class='py-1'>{$item->quantity}</div>";
                        }
                        return implode('', $list);
                    }),

                TextColumn::make('item_price')
                    ->label('Harga')
                    ->html()
                    ->alignLeft()
                    ->state(function (\App\Models\Sale $record) {
                        $list = [];
                        foreach ($record->salesItems as $item) {
                            $price = number_format($item->price, 0, ',', '.');
                            $list[] = "<div class='py-1'>Rp {$price}</div>";
                        }
                        return implode('', $list);
                    }),

                TextColumn::make('item_total')
                    ->label('Harga Total')
                    ->html()
                    ->alignLeft()
                    ->state(function (\App\Models\Sale $record) {
                        $list = [];
                        foreach ($record->salesItems as $item) {
                            $total = number_format($item->total, 0, ',', '.');
                            $list[] = "<div class='py-1 font-bold'>Rp {$total}</div>";
                        }
                        return implode('', $list);
                    }),

                TextColumn::make('total_amount')
                    ->label('Total Transaksi')
                    ->money('idr')
                    ->sortable()
                    ->state(function (\App\Models\Sale $record) {
                         // Hitung ulang total dari item untuk memastikan akurasi
                         return $record->salesItems->sum('total');
                    }),

                TextColumn::make('sales_items_count')
                    ->label('Jumlah Item')
                    ->counts('salesItems')
                    ->alignCenter(),
            ])
            ->defaultSort('sale_date', 'desc')
            ->filters([
                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn($query) => $query->whereDate('sale_date', today())),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->roles->whereIn('name', ['admin', 'owner'])->isNotEmpty() ?? false),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()?->roles->whereIn('name', ['admin', 'owner'])->isNotEmpty() ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->roles->whereIn('name', ['admin', 'owner'])->isNotEmpty() ?? false),
                ]),
            ]);
    }
}
