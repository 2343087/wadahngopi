<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cafe_id')
                    ->relationship('cafe', 'name', modifyQueryUsing: function (Builder $query) {
                        // If Admin, show all. If Owner, show only owned cafes.
                        if (auth()->user()->role !== 'admin') {
                            $query->where('owner_id', auth()->id());
                        }
                    })
                    ->visible(fn () => auth()->user()->role === 'admin' || auth()->user()->cafes()->count() > 1)
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(fn () => auth()->user()->role !== 'admin' ? auth()->user()->cafes()->first()?->id : null),

                // Hidden field for single-cafe owners to ensuring data integrity
                Forms\Components\Hidden::make('cafe_id')
                    ->default(fn () => auth()->user()->role !== 'admin' ? auth()->user()->cafes()->first()?->id : null)
                    ->visible(fn () => auth()->user()->role !== 'admin' && auth()->user()->cafes()->count() <= 1),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Select::make('type')
                    ->options([
                        'coffee' => 'Kopi',
                        'non-coffee' => 'Non-Kopi',
                        'food' => 'Makanan',
                    ])
                    ->required(),
                Forms\Components\FileUpload::make('image_path')
                    ->image()
                    ->directory('menus')
                    ->visibility('public'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role !== 'admin') {
            // Join tables to filter menus by cafe ownership
            $query->whereHas('cafe', function (Builder $q) {
                $q->where('owner_id', auth()->id());
            });
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Foto'),
                Tables\Columns\TextColumn::make('cafe.name')
                    ->label('Cafe')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'coffee' => 'primary',
                        'non-coffee' => 'info',
                        'food' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cafe')
                    ->relationship('cafe', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
