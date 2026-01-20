@extends('layouts.app')

@section('title', 'Tersimpan - WadahNgopi.Com')

@section('content')
    <style>
        .saved-header {
            padding: 50px 20px 25px;
            background: transparent;
        }

        .saved-header h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            margin-bottom: 6px;
            letter-spacing: -1px;
        }

        .saved-list {
            padding: 0 20px 40px;
        }

        .empty-state {
            text-align: center;
            padding: 120px 25px;
            color: var(--color-text-muted);
        }

        .empty-state i {
            font-size: 4.5rem;
            opacity: 0.1;
            margin-bottom: 25px;
            display: block;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            margin-bottom: 10px;
        }

        .cafe-item {
            display: flex;
            gap: 16px;
            padding: 14px;
            background: white;
            border-radius: 26px;
            margin-bottom: 14px;
            text-decoration: none;
            color: inherit;
            border: 1px solid rgba(0, 0, 0, 0.02);
            box-shadow: var(--shadow-sm);
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .cafe-item:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: rgba(111, 78, 55, 0.06);
        }

        .remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 32px;
            height: 32px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-text-muted);
            z-index: 10;
            transition: var(--transition-smooth);
        }

        .remove-btn:hover {
            background: #FEE2E2;
            color: #EF4444;
            transform: scale(1.1);
        }

        .cafe-item-image {
            width: 85px;
            height: 85px;
            border-radius: 18px;
            object-fit: cover;
        }

        .cafe-item-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .cafe-item-info h3 {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            margin-bottom: 4px;
        }

        .cafe-item-info p {
            font-size: 0.8rem;
            color: var(--color-text-muted);
            margin-bottom: 8px;
            font-weight: 500;
        }

        .badge-row {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .rating-tag {
            color: #f59e0b;
            font-weight: 800;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .wifi-tag {
            background: rgba(74, 93, 35, 0.08);
            color: var(--color-earth-green);
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div x-data="{ 
                bookmarks: JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]'),
                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const urlIds = urlParams.getAll('ids[]');

                    if (this.bookmarks.length > 0 && urlIds.length === 0) {
                        let query = this.bookmarks.map(id => `ids[]=${id}`).join('&');
                        window.location.href = `{{ route('saved') }}?${query}`;
                    }
                },
                removeBookmark(id) {
                    this.bookmarks = this.bookmarks.filter(b => b != id);
                    localStorage.setItem('wadah-bookmarks', JSON.stringify(this.bookmarks));

                    if (this.bookmarks.length === 0) {
                        window.location.href = `{{ route('saved') }}`;
                    } else {
                        let query = this.bookmarks.map(id => `ids[]=${id}`).join('&');
                        window.location.href = `{{ route('saved') }}?${query}`;
                    }
                }
            }">
        <div class="saved-header">
            <h1>Cafe Tersimpan</h1>
            <p x-show="bookmarks.length > 0" style="color: var(--color-text-muted); font-size: 0.9rem;">
                Daftar cafe favorit yang kamu simpan.
            </p>
        </div>

        <div class="saved-list">
            @forelse($cafes as $index => $cafe)
                <div class="cafe-item animate-up" style="animation-delay: {{ $index * 0.05 }}s">
                    <a @click.prevent="removeBookmark({{ $cafe->id }})" class="remove-btn">
                        <i class="bi bi-x"></i>
                    </a>
                    <a href="{{ route('cafes.show', $cafe) }}"
                        style="display: flex; gap: 16px; text-decoration: none; color: inherit; flex: 1;">
                        <img src="{{ $cafe->image_path ?? 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&q=80&w=800' }}"
                            alt="{{ $cafe->name }}" class="cafe-item-image" loading="lazy">
                        <div class="cafe-item-info">
                            <h3 class="line-clamp-1" x-text="'{{ $cafe->name }}'"></h3>
                            <p class="line-clamp-1">{{ $cafe->address }}</p>
                            <div class="badge-row">
                                <span class="rating-tag">
                                    <i class="bi bi-star-fill"></i> {{ $cafe->rating ?? '0.0' }}
                                </span>
                                @if ($cafe->has_wifi)
                                    <span class="wifi-tag">Free WiFi</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="empty-state animate-up">
                    <i class="bi bi-bookmark-heart"></i>
                    <h3>Belum Ada Simpanan</h3>
                    <p>Jelajahi dan simpan cafe favoritmu!</p>
                    <a href="{{ route('explore') }}" class="btn btn-primary"
                        style="margin-top: 30px; border-radius: 18px;">Mulai Explore</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection