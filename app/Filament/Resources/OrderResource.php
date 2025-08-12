<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;

class OrderResource extends Resource
{
    protected static ?int $navigationSort = 4; // برای محصولات

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('customer_id')
                ->label('Customer')
                ->relationship('customer', 'name')
                ->required(),
            Forms\Components\DateTimePicker::make('order_date')->required(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'canceled' => 'Canceled',
                ])->default('pending')
                ->required(),
            Forms\Components\TextInput::make('total_amount')->numeric()->required(),
            Forms\Components\Select::make('payment_status')
                ->options([
                    'unpaid' => 'Unpaid',
                    'paid' => 'Paid',
                ])->default('unpaid')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->sortable(),
            Tables\Columns\TextColumn::make('customer.name')->label('Customer')->sortable(),
            Tables\Columns\TextColumn::make('order_date')->dateTime()->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->getStateUsing(fn($record) => match ($record->status) {
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'canceled' => 'Canceled',
                    default => 'Unknown',
                })
                ->colors([
                    'warning' => 'pending',
                    'primary' => 'processing',
                    'success' => 'completed',
                    'danger' => 'canceled',
                ])
                ->sortable()
                ->searchable()
                ->toggleable(),


            Tables\Columns\TextColumn::make('payment_status')->sortable(),
            Tables\Columns\TextColumn::make('total_amount')->sortable(),
        ])->filters([
            Filter::make('customer_name')
                ->form([
                    Forms\Components\TextInput::make('customer_name')->label('نام مشتری'),
                ])
                ->query(function ($query, array $data) {
                    return $query->when(
                        $data['customer_name'],
                        fn($q) =>
                        $q->whereHas(
                            'customer',
                            fn($q2) =>
                            $q2->where('name', 'like', "%{$data['customer_name']}%")
                        )
                    );
                }),

            // فیلتر بر اساس وضعیت سفارش
            SelectFilter::make('status')
                ->label('وضعیت سفارش')
                ->options([
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'completed' => 'Completed',
                    'canceled' => 'Canceled',
                ]),

            // فیلتر بر اساس وضعیت پرداخت
            SelectFilter::make('payment_status')
                ->label('وضعیت پرداخت')
                ->options([
                    'unpaid' => 'Unpaid',
                    'paid' => 'Paid',
                ]),



        ])
            ->bulkActions([]);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
