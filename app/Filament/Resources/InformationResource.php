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

    protected static ?string $navigationLabel = 'Info & Berita Kopi';

    protected static ?string $modelLabel = 'Berita';

    protected static ?string $pluralModelLabel = 'Berita Kopi';

    protected static ?string $navigationGroup = 'Konten & Promo';

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Apa Beritanya? ✍️')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Tulis judul yang bikin orang pengen klik...')
                            ->label('Judul Postingan')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('link-otomatis-disini')
                            ->label('Link Cantik (Slug)')
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->options([
                                'Berita' => 'Berita Hangat',
                                'Edukasi' => 'Kelas Kopi',
                                'Lomba' => 'Event & Lomba',
                                'Promo' => 'Diskon & Promo',
                            ])
                            ->default('Berita')
                            ->label('Masuk Kategori Apa?')
                            ->required(),
                        Forms\Components\Textarea::make('summary')
                            ->rows(3)
                            ->placeholder('Ringkas dikit beritanya biar orang makin penasaran...')
                            ->label('Ringkasan Singkat')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->placeholder('Tulis isi beritanya disini ya boss...')
                            ->label('Isi Konten Lengkap')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Biar Makin Estetik 🖼️')
                    ->schema([
                        Forms\Components\FileUpload::make('image_path')
                            ->image()
                            ->disk('public')
                            ->directory('information')
                            ->visibility('public')
                            ->maxSize(5120) // 5MB limit
                            ->label('Foto Utama Postingan'),
                        Forms\Components\Toggle::make('is_published')
                            ->label('Tayangin Langsung?')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Kapan Tayangnya?')
                            ->default(now()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->weight('bold')
                    ->wrap()
                    ->description(fn (Information $record): string => $record->category),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
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
