<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?int $navigationSort = 3; // برای محصولات

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->required(),

                Forms\Components\TextInput::make('name')->required()->maxLength(255),

                Forms\Components\Textarea::make('description')->rows(3),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('cost_price')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('stock_quantity')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('sku')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('image_url')
                    ->url()
                    ->nullable(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable(),
                Tables\Columns\TextColumn::make('price')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('cost_price')->sortable()->toggleable(),

                Tables\Columns\TextColumn::make('stock_quantity')->sortable()
            ])
            ->filters([
                // فیلتر بر اساس دسته‌بندی (رابطه category)
                SelectFilter::make('category_id')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name'),

                // فیلتر بر اساس نام محصول (جستجوی متنی)
                Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')->label('نام محصول'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query->when($data['name'], fn($q) => $q->where('name', 'like', "%{$data['name']}%"));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
