<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FacilityResource\Pages;
use App\Models\Facility;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FacilityResource extends Resource
{
    protected static ?string $model = Facility::class;

    protected static ?string $navigationIcon = 'heroicon-o-swatch';

    protected static ?string $navigationLabel = 'Fasilitas Cafe';

    protected static ?string $modelLabel = 'Fasilitas';

    protected static ?string $pluralModelLabel = 'Layanan Fasilitas';

    protected static ?string $navigationGroup = 'Manajemen Warung';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Fasilitas Menarik 🛠️')
                    ->description('Apa aja sih yang bikin betah di cafe kamu?')
                    ->schema([
                        // Auto-fill cafe_id with the logged-in Owner's cafe
                        Forms\Components\Hidden::make('cafe_id')
                            ->default(fn () => auth()->user()->cafes()->first()?->id)
                            ->required()
                            ->rules(
                                fn () => auth()->user()?->role === 'developer'
                                ? ['exists:cafes,id']
                                : ['exists:cafes,id,owner_id,'.auth()->id()]
                            ),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: WiFi Kenceng, Area Rooftop')
                            ->label('Nama Fasilitasnya'),
                        Forms\Components\TextInput::make('icon')
                            ->placeholder('bi-wifi')
                            ->maxLength(255)
                            ->label('Icon Bootstrap')
                            ->helperText('Gunakan kode icon dari Bootstrap Icons, misal: bi-wifi, bi-cup-hot, bi-wind (buat AC)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('icon')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                //
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

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->role === 'admin') {
            // Only show facilities belonging to the Owner's cafe
            $query->whereHas('cafe', function (Builder $q) {
                $q->where('owner_id', auth()->id());
            });
        } else {
            // Developer and others see nothing
            $query->whereRaw('1 = 0');
        }

        return $query;
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
            'index' => Pages\ListFacilities::route('/'),
            'create' => Pages\CreateFacility::route('/create'),
            'edit' => Pages\EditFacility::route('/{record}/edit'),
        ];
    }
}
