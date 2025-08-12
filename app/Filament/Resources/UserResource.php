<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 7; // برای محصولات

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // TextInput::make('id'),
                TextInput::make('name'),
                TextInput::make('email'),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password() // این باعث می‌شود فیلد به صورت پسورد نمایش داده شود
                    ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord) // فقط در صفحه ایجاد الزامی است
                    ->minLength(8)
                    ->dehydrateStateUsing(fn($state) => $state ? bcrypt($state) : null) // هش کردن پسورد فقط اگر مقدار وارد شده باشد
                    ->dehydrated(fn($state) => filled($state)) // اگر مقدار خالی بود، مقدار به دیتابیس ارسال نشود (برای ویرایش)
                    ->autocomplete('new-password'), // کمک به مرورگر برای تشخیص فیلد پسورد


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
