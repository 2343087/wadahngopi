<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CafeResource\Pages;
use App\Models\Cafe;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CafeResource extends Resource
{
    protected static ?string $model = Cafe::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION: ADMIN CONTROL (Structural) - Hidden from Owner
                Forms\Components\Section::make('Admin Control')
                    ->description('Hanya Admin yang bisa mengatur ini.')
                    ->schema([
                        Forms\Components\Select::make('owner_id')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Pemilik Cafe')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'review' => 'Under Review',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Cafe'),

                        Forms\Components\TextInput::make('rating')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(5)
                            ->label('Rating (Manual)'),
                    ])
                    ->columns(2)
                    ->visible(fn () => auth()->user()->role === 'admin'),

                // SECTION: OWNER CONTENT (Content) - Hidden from Admin
                Forms\Components\Section::make('Informasi Cafe')
                    ->description('Kelola detail cafe Anda di sini.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->readOnly() // Owner cannot change name provided by Admin
                            ->label('Nama Cafe'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'review' => 'Ajukan Review',
                                'published' => 'Publish Sekarang',
                            ])
                            ->label('Status Publikasi'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image_path')
                            ->image()
                            ->directory('cafes')
                            ->visibility('public')
                            ->label('Gambar Utama (Cover)'),

                        Forms\Components\FileUpload::make('images')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->directory('cafes/gallery')
                            ->visibility('public')
                            ->label('Galeri Gambar (Max 5)')
                            ->reorderable()
                            ->columnSpanFull(),

                        Forms\Components\Section::make('Lokasi & Kontak')
                            ->schema([
                                Forms\Components\Textarea::make('address')
                                    ->required()
                                    ->label('Alamat Lengkap')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('google_maps_url')
                                    ->url()
                                    ->label('Link Google Maps'),
                                Forms\Components\TextInput::make('whatsapp_number')
                                    ->tel()
                                    ->label('Nomor WhatsApp'),
                            ])->columns(2),

                        Forms\Components\Section::make('Jam Operasional & Koordinat')
                            ->schema([
                                Forms\Components\TimePicker::make('opening_time')->label('Buka'),
                                Forms\Components\TimePicker::make('closing_time')->label('Tutup'),
                                Forms\Components\TextInput::make('latitude')->numeric(),
                                Forms\Components\TextInput::make('longitude')->numeric(),
                            ])->columns(2),

                    ])
                    ->visible(fn () => auth()->user()->role !== 'admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Foto')
                    ->visible(fn () => auth()->user()->role !== 'admin'), // Admin might not care about photo

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Cafe'),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->visible(fn () => auth()->user()->role === 'admin'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'review',
                        'success' => 'published',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->visible(fn () => auth()->user()->role === 'admin'), // Only Admin cares about managing rating
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role !== 'admin') {
            $query->where('owner_id', auth()->id());
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
            'index' => Pages\ListCafes::route('/'),
            'create' => Pages\CreateCafe::route('/create'),
            'edit' => Pages\EditCafe::route('/{record}/edit'),
        ];
    }
}
