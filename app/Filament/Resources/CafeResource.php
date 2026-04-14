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

    protected static ?string $navigationLabel = 'Daftar Cafe Kita';

    protected static ?string $modelLabel = 'Cafe';

    protected static ?string $pluralModelLabel = 'Daftar Cafe';

    protected static ?string $navigationGroup = 'Manajemen Warung';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canCreate(): bool
    {
        return in_array(auth()->user()?->role, ['developer', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION 1: SHARED CORE INFO
                Forms\Components\Section::make('Informasi Utama ☕')
                    ->description('Data dasar cafe kamu.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Kopi Malem Jumat')
                            ->label('Nama Cafe')
                            ->readOnly(fn () => auth()->user()?->role === 'admin'),

                        Forms\Components\Select::make('city_id')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Kota Lokasi')
                            ->placeholder('Pilih kota...')
                            ->required()
                            ->disabled(fn () => auth()->user()?->role === 'admin')
                            ->dehydrated(),
                    ])
                    ->columns(2),

                // SECTION 2: ADMIN/DEV CONTROLS
                Forms\Components\Section::make('Kendali Admin 🛠️')
                    ->description('Hanya tim internal yang bisa otir-atik bagian ini.')
                    ->schema([
                        Forms\Components\Select::make('owner_id')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Siapa Pemiliknya?')
                            ->placeholder('Pilih bos cafe-nya...')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Masih Draft',
                                'review' => 'Lagi Di-Review',
                                'published' => 'Udah Tayang',
                            ])
                            ->label('Status Sekarang')
                            ->default('draft')
                            ->required()
                            ->visible(fn () => auth()->user()?->role === 'developer'),
                        
                        Forms\Components\Select::make('status')
                            ->options([
                                'review' => 'Minta Approval Admin',
                                'published' => 'Langsung Tayangin!',
                            ])
                            ->label('Mau Diapain?')
                            ->required()
                            ->visible(fn () => auth()->user()?->role === 'admin'),
                    ])
                    ->columns(2)
                    ->visible(fn () => in_array(auth()->user()?->role, ['developer', 'admin'])),

                // SECTION: MAIN CONTENT
                Forms\Components\Section::make('Detail Kece Cafe Kamu ✨')
                    ->description('Isi semua info biar orang-orang pada mampir!')
                    ->schema([
                        Forms\Components\Section::make('Katalog / Lembaran Menu (Gambar) 📑')
                            ->description('Cara paling gampang! Cukup upload foto daftar menu kamu (lembaran/katalog). User tinggal klik buat nge-zoom.')
                            ->schema([
                                Forms\Components\Repeater::make('menu_images')
                                    ->label('Foto Daftar Menu')
                                    ->schema([
                                        Forms\Components\FileUpload::make('image')
                                            ->image()
                                            ->directory('cafes/menu-gallery')
                                            ->visibility('public')
                                            ->label('Upload Foto Menu')
                                            ->required()
                                            ->maxSize(5120)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('tag')
                                            ->label('Kategori')
                                            ->placeholder('Misal: Menu Utama, Promo, Minuman')
                                            ->required()
                                            ->columnSpan(2),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(4)
                                    ->reorderable()
                                    ->addActionLabel('+ Tambah Halaman Menu')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\RichEditor::make('description')

                            ->label('Tentang Cafe Kamu')
                            ->placeholder('Ceritain dong apa yang bikin cafe kamu spesial...')
                            ->fileAttachmentsDirectory('cafes/description-images')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('image_path')
                            ->image()
                            ->directory('cafes')
                            ->visibility('public')
                            ->label('Foto Profil Utama')
                            // ->imageResizeMode('cover')
                            // ->imageCropAspectRatio('1:1')
                            // ->imageResizeTargetWidth('1080')
                            // ->imageResizeTargetHeight('1080')
                            ->maxSize(5120) // 5MB Limit
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),

                        Forms\Components\FileUpload::make('images')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->directory('cafes/gallery')
                            ->visibility('public')
                            ->label('Galeri Foto (Biar Makin Estetik)')
                            ->helperText('Maksimal 5 foto ya boss, usahain yang resolusinya mantap!')
                            ->reorderable()
                            // ->imageResizeMode('cover')
                            // ->imageResizeTargetWidth('1280')
                            // ->imageResizeTargetHeight('1280')
                            ->maxSize(5120) // 5MB Limit
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->columnSpanFull(),

                        Forms\Components\Section::make('Fasilitas Cafe 🛋️')
                            ->description('Apa aja yang ada di cafe kamu?')
                            ->schema([
                                Forms\Components\Repeater::make('facilities')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Fasilitas')
                                            ->placeholder('Contoh: Wifi Kenceng, Musholla, AC Dingin')
                                            ->required(),
                                    ])
                                    ->columns(1)
                                    ->defaultItems(0)
                                    ->addActionLabel('+ Tambah Fasilitas')
                                    ->grid(3)
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Section::make('Cara Kesini & Kontak 📍')
                            ->schema([
                                Forms\Components\Textarea::make('address')
                                    ->required()
                                    ->label('Alamat Lengkapnya')
                                    ->placeholder('Tulis alamat yang bener biar kaga nyasar...')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('google_maps_url')
                                    ->url()
                                    ->placeholder('https://maps.google.com/...')
                                    ->label('Link Google Maps'),
                                Forms\Components\TextInput::make('whatsapp_number')
                                    ->tel()
                                    ->placeholder('0812xxxxxxxx')
                                    ->label('Nomor WhatsApp (Aktif)')
                                    ->helperText('Format bebas (08xx atau 628xx). Sistem otomatis konversi ke link WA.'),
                            ])->columns(2),

                        Forms\Components\Section::make('Nongkrong Jam Berapa? 🕒')
                            ->schema([
                                // Toggle 24 Jam
                                Forms\Components\Toggle::make('is_24_hours')
                                    ->label('Buka 24 Jam Non-Stop 🔥')
                                    ->helperText('Aktifkan jika cafe kamu buka seharian penuh!')
                                    ->live()
                                    ->columnSpanFull(),

                                // Jam Weekday (Senin-Jumat)
                                Forms\Components\Fieldset::make('Jam Hari Kerja (Senin - Jumat)')
                                    ->schema([
                                        Forms\Components\TimePicker::make('operating_hours.weekday.open')
                                            ->label('Buka')
                                            ->seconds(false)
                                            ->format('H:i')
                                            ->displayFormat('H:i'),
                                        Forms\Components\TimePicker::make('operating_hours.weekday.close')
                                            ->label('Tutup')
                                            ->seconds(false)
                                            ->format('H:i')
                                            ->displayFormat('H:i'),
                                    ])
                                    ->columns(2)
                                    ->hidden(fn (Forms\Get $get): bool => (bool) $get('is_24_hours')),

                                // Jam Weekend (Sabtu-Minggu)
                                Forms\Components\Fieldset::make('Jam Akhir Pekan (Sabtu - Minggu)')
                                    ->schema([
                                        Forms\Components\TimePicker::make('operating_hours.weekend.open')
                                            ->label('Buka')
                                            ->seconds(false)
                                            ->format('H:i')
                                            ->displayFormat('H:i'),
                                        Forms\Components\TimePicker::make('operating_hours.weekend.close')
                                            ->label('Tutup')
                                            ->seconds(false)
                                            ->format('H:i')
                                            ->displayFormat('H:i'),
                                    ])
                                    ->columns(2)
                                    ->hidden(fn (Forms\Get $get): bool => (bool) $get('is_24_hours')),

                                Forms\Components\ViewField::make('location_trigger')
                                    ->view('filament.components.location-button')
                                    ->hiddenLabel(),
                                Forms\Components\TextInput::make('latitude')
                                    ->numeric()
                                    ->placeholder('Contoh: -6.xxxx')
                                    ->suffixAction(
                                        Forms\Components\Actions\Action::make('getLocation')
                                            ->icon('heroicon-m-map-pin')
                                            ->tooltip('Ambil Lokasi Saya')
                                            ->action(function () {})
                                            ->extraAttributes([
                                                'class' => 'cursor-pointer text-primary-500',
                                                'title' => 'Ambil Lokasi Saat Ini',
                                                'x-on:click.prevent' => <<<'JS'
                                                    if (!navigator.geolocation) {
                                                        alert('Browser kamu tidak mendukung geolocation.');
                                                        return;
                                                    }
                                                    navigator.geolocation.getCurrentPosition(
                                                        (position) => {
                                                            $wire.set('data.latitude', position.coords.latitude);
                                                            $wire.set('data.longitude', position.coords.longitude);
                                                            new Notification('Lokasi Berhasil Diambil!');
                                                        },
                                                        (error) => {
                                                            console.error(error);
                                                            alert('Gagal mengambil lokasi: ' + error.message);
                                                        }
                                                    );
                                                JS,
                                            ])
                                    ),
                                Forms\Components\TextInput::make('longitude')
                                    ->numeric()
                                    ->placeholder('Contoh: 106.xxxx'),
                            ])->columns(2),

                        Forms\Components\Section::make('Social Media 📱')
                            ->description('Tambahin link sosmedmu biar makin gampang dikenal!')
                            ->schema([
                                Forms\Components\Repeater::make('social_links')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\Select::make('platform')
                                            ->options([
                                                'instagram' => 'Instagram',
                                                'tiktok' => 'TikTok',
                                                'facebook' => 'Facebook',
                                                'twitter' => 'Twitter/X',
                                            ])
                                            ->required()
                                            ->placeholder('Pilih platform...')
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('url')
                                            ->url()
                                            ->placeholder('https://...')
                                            ->columnSpan(2),
                                        Forms\Components\Toggle::make('show')
                                            ->label('Tampilkan')
                                            ->default(true)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(4)
                                    ->defaultItems(0)
                                    ->maxItems(4)
                                    ->addActionLabel('+ Tambah Sosmed')
                                    ->reorderable(false)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->visible(fn () => in_array(auth()->user()?->role, ['developer', 'admin'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('Foto')
                    ->visible(fn () => auth()->user()?->role === 'admin'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Nama Cafe'),

                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->toggleable()
                    ->visible(fn () => auth()->user()?->role === 'developer'),

                Tables\Columns\TextColumn::make('city.name')
                    ->label('Kota')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'review' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'review' => 'Pending Review',
                        'published' => 'Published',
                        default => $state,
                    })
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Pending Review',
                        'published' => 'Published',
                    ]),
                Tables\Filters\SelectFilter::make('city_id')
                    ->label('Berdasarkan Kota')
                    ->relationship('city', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->url(fn (Cafe $record): string => static::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()->with(['owner']);

        if (auth()->user()?->role === 'admin') {
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
