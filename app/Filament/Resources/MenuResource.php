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

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Detail Menu (Opsional)';

    protected static ?string $modelLabel = 'Menu Satuan';

    protected static ?string $pluralModelLabel = 'Daftar Menu Satuan';

    protected static ?string $navigationGroup = 'Manajemen Warung';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ayo Masukin Menu Baru! 🍽️')
                    ->description('Bikin menu yang bikin orang laper liatnya.')
                    ->schema([
                        Forms\Components\Placeholder::make('gallery_hint')
                            ->label('MALES INPUT SATU-SATU?')
                            ->content(view('filament-hints.menu-gallery-link'))
                            ->columnSpanFull(),
                        Forms\Components\Select::make('cafe_id')
                            ->relationship('cafe', 'name', modifyQueryUsing: function (Builder $query) {
                                // If Admin, show all. If Owner, show only owned cafes.
                                if (auth()->user()->role !== 'admin') {
                                    $query->where('owner_id', auth()->id());
                                }
                            })
                            ->visible(fn() => auth()->user()->role === 'admin' || auth()->user()->cafes()->count() > 1)
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Punya Cafe Mana?')
                            ->default(fn() => auth()->user()->role !== 'admin' ? auth()->user()->cafes()->first()?->id : null),

                        // Hidden field for single-cafe owners to ensuring data integrity
                        Forms\Components\Hidden::make('cafe_id')
                            ->default(fn() => auth()->user()->role !== 'admin' ? auth()->user()->cafes()->first()?->id : null)
                            ->visible(fn() => auth()->user()->role !== 'admin' && auth()->user()->cafes()->count() <= 1),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Es Kopi Susu Mantan')
                            ->label('Nama Menunya'),

                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('Misal: 15000')
                            ->label('Harga Jual'),

                        Forms\Components\Select::make('type')
                            ->options(fn() => Menu::query()->pluck('type')->unique()->push('coffee', 'non-coffee', 'food')->unique()->mapWithKeys(fn($t) => [$t => ucwords(str_replace('-', ' ', $t))])->toArray())
                            ->label('Masuk Kategori Apa?')
                            ->required()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('type')
                                    ->required()
                                    ->label('Kategori Baru'),
                            ])
                            ->placeholder('Pilih atau buat kategori baru...')
                            ->hint('Kategori ini akan muncul sebagai Tab di halaman Cafe.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Tampilkan Menu Ini?')
                            ->helperText('Matikan jika menu lagi kosong/habis.')
                            ->default(true)
                            ->required(),

                        Forms\Components\FileUpload::make('image_path')
                            ->image()
                            ->directory('menus')
                            ->visibility('public')
                            ->label('Foto Menu (Biar Tergiur)')
                            ->columnSpanFull(),
                    ])->columns(2),
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
                    ->circular()
                    ->label('Foto'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('cafe.name')
                    ->label('Cafe')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: auth()->user()->role !== 'admin'),

                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'coffee' => 'primary',
                        'non-coffee' => 'info',
                        'food' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
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
