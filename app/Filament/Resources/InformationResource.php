<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InformationResource\Pages;
use App\Models\Information;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InformationResource extends Resource
{
    protected static ?string $model = Information::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Informasi Kopi';

    protected static ?string $navigationGroup = 'Admin Only';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Konten Utama')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->options([
                                'Berita' => 'Berita',
                                'Edukasi' => 'Edukasi',
                                'Lomba' => 'Lomba',
                                'Promo' => 'Promo',
                            ])
                            ->default('Berita')
                            ->required(),
                        Forms\Components\Textarea::make('summary')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Media & Publikasi')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->image()
                            ->directory('information')
                            ->label('Gambar Utama'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publikasikan')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->default(now()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_path'),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInformation::route('/'),
            'create' => Pages\CreateInformation::route('/create'),
            'edit' => Pages\EditInformation::route('/{record}/edit'),
        ];
    }
}
