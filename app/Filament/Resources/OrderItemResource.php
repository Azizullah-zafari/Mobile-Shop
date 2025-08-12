<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Filament\Resources\OrderItemResource\RelationManagers;
use App\Models\OrderItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;
    protected static ?int $navigationSort = 5; // برای محصولات

    protected static ?string $navigationIcon = 'heroicon-o-ellipsis-horizontal';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('order_id')
                ->label('Order')
                ->options(
                    \App\Models\Order::with('customer')->get()->mapWithKeys(function ($order) {
                        return [$order->id => 'Order #' . $order->id . ' - ' . $order->customer->name . ' - '];
                    })->toArray()
                )
                ->searchable()
                ->required(),


            Select::make('product_id')
                ->label('Product')
                ->relationship('product', 'name')
                ->required()
                ->reactive()
                ->afterStateUpdated(fn(callable $set, $state) => self::setUnitPrice($set, $state)),

            TextInput::make('quantity')
                ->numeric()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn(callable $set, $state, $get) => self::setTotalPrice($set, $state, $get)),

            TextInput::make('unit_price')
                ->numeric()
                ->required(),


            TextInput::make('total_price')
                ->numeric()
                ->required(),

        ]);
    }

    // متد کمکی برای گرفتن قیمت واحد محصول و ست کردن آن
    protected static function setUnitPrice(callable $set, $productId)
    {
        $price = \App\Models\Product::find($productId)?->price ?? 0;
        $set('unit_price', $price);
        $set('total_price', 0);
    }

    protected static function setTotalPrice(callable $set, $quantity, $get)
    {
        $unitPrice = $get('unit_price') ?? 0;
        $total = $unitPrice * $quantity;
        $set('total_price', $total);
    }


    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('order.id')->label('Order')->sortable(),
            Tables\Columns\TextColumn::make('product.name')->label('Product')->sortable(),
            Tables\Columns\TextColumn::make('quantity')->sortable(),

            Tables\Columns\TextColumn::make('unit_price')->sortable(),
            Tables\Columns\TextColumn::make('total_price')->sortable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
    protected static function booted()
    {
        static::created(function ($orderItem) {
            $product = $orderItem->product;

            // کاهش موجودی
            $product->stock_quantity -= $orderItem->quantity;

            // اگر موجودی صفر یا کمتر شد، محصول ناموجود می‌شود
            if ($product->stock_quantity <= 0) {
                $product->stock_quantity = 0;
                $product->is_available = false;
            }

            $product->save();
        });

        // اگر نیاز به بازگرداندن موجودی در صورت حذف آیتم سفارش بود، این هم اضافه کن:
        static::deleted(function ($orderItem) {
            $product = $orderItem->product;
            $product->stock_quantity += $orderItem->quantity;
            if ($product->stock_quantity > 0) {
                $product->is_available = true;
            }
            $product->save();
        });
    }
}
