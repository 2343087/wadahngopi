@extends('layouts.app')

@section('title', $cafe->name . ' - WadahNgopi.Com')

@section('content')
    <style>
        .detail-header {
            position: relative;
            height: 320px;
            overflow: hidden;
        }

        .detail-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .detail-header:hover .detail-image {
            transform: scale(1.05);
        }

        .back-btn,
        .bookmark-btn {
            position: absolute;
            top: 25px;
            width: 44px;
            height: 44px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-coffee-dark);
            z-index: 10;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: var(--transition-smooth);
            border: 1px solid rgba(111, 78, 55, 0.05);
        }

        .back-btn {
            left: 20px;
        }

        .bookmark-btn {
            right: 20px;
            cursor: pointer;
        }

        .back-btn:active,
        .bookmark-btn:active {
            transform: scale(0.9);
        }

        .detail-card {
            margin: -45px 20px 0;
            padding: 28px;
            position: relative;
            z-index: 5;
            background: white;
            border-radius: 32px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .cafe-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            margin-bottom: 8px;
            letter-spacing: -1px;
        }

        .cafe-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            color: var(--color-text-muted);
            font-weight: 500;
        }

        .cafe-rating i {
            color: #F59E0B;
        }

        .cafe-rating b {
            color: var(--color-text);
            font-weight: 800;
        }

        .action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 28px;
            padding: 0 20px;
        }

        .menu-section {
            padding: 35px 20px 40px;
        }

        .menu-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            overflow-x: auto;
            padding-bottom: 5px;
            scrollbar-width: none;
        }

        .menu-tabs::-webkit-scrollbar {
            display: none;
        }

        .tab {
            padding: 9px 20px;
            border-radius: 14px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            background: white;
            color: var(--color-text-muted);
            transition: var(--transition-smooth);
            white-space: nowrap;
            border: 1px solid rgba(111, 78, 55, 0.06);
        }

        .tab.active {
            background: var(--color-coffee-dark);
            color: white;
            border-color: var(--color-coffee-dark);
            box-shadow: 0 6px 15px rgba(62, 39, 35, 0.18);
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px;
            background: white;
            border-radius: 24px;
            margin-bottom: 14px;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(0, 0, 0, 0.02);
            transition: var(--transition-smooth);
        }

        .menu-img {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            object-fit: cover;
        }

        .menu-info {
            flex: 1;
        }

        .menu-info h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--color-coffee-dark);
            margin-bottom: 3px;
        }

        .menu-info .price {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--color-coffee);
        }

        .section-header {
            margin-bottom: 22px;
        }

        .section-header h3 {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            letter-spacing: -0.5px;
        }

        .rating-input {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin: 20px 0;
        }

        .rating-input i {
            font-size: 2.2rem;
            cursor: pointer;
            transition: var(--transition-smooth);
            color: #e2e8f0;
        }

        .rating-input i.active {
            color: #F59E0B;
            transform: scale(1.15);
            filter: drop-shadow(0 0 8px rgba(245, 158, 11, 0.3));
        }

        .rating-section {
            background: var(--color-cream-dark);
            border-radius: 20px;
            padding: 24px;
            margin-top: 25px;
            text-align: center;
        }

        .rating-section h4 {
            font-weight: 800;
            color: var(--color-coffee-dark);
            margin-bottom: 8px;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>

    <div x-data="{ 
            saved: JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]').includes({{ $cafe->id }}),
            currentTab: 'coffee',
            activeTabs: ['coffee', 'non-coffee', 'food'],
            userRating: 0, 
            isSubmitting: false, 
            isSuccess: false,
            hoverRating: 0,
            avgRating: '{{ number_format($cafe->rating, 1) }}',

            toggleBookmark() {
                let bookmarks = JSON.parse(localStorage.getItem('wadah-bookmarks') || '[]');
                if (this.saved) {
                    bookmarks = bookmarks.filter(id => id !== {{ $cafe->id }});
                } else {
                    bookmarks.push({{ $cafe->id }});
                }
                localStorage.setItem('wadah-bookmarks', JSON.stringify(bookmarks));
                this.saved = !this.saved;
            },

            submitRating() {
                if (this.userRating === 0) return;
                this.isSubmitting = true;

                fetch('{{ route('cafes.review', $cafe) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        rating: this.userRating,
                        user_name: 'Buan Ngopi'
                    })
                })
                .then(r => r.json())
                .then(data => {
                    this.isSubmitting = false;
                    this.isSuccess = true;
                    this.avgRating = data.new_rating;
                });
            }
        }">
        <div class="detail-header">
            <a href="javascript:history.back()" class="back-btn">
                <i class="bi bi-chevron-left"></i>
            </a>
            <div class="bookmark-btn" @click="toggleBookmark">
                <i :class="saved ? 'bi bi-bookmark-fill' : 'bi bi-bookmark'"
                    :style="saved ? 'color: var(--color-coffee)' : ''"></i>
            </div>
            <img src="{{ $cafe->image_path ?? 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&q=80&w=1200' }}"
                alt="{{ $cafe->name }}" class="detail-image">
        </div>

        <div class="detail-card animate-up">
            <h1 class="cafe-title">{{ $cafe->name }}</h1>
            <div class="cafe-rating">
                <i class="bi bi-star-fill"></i>
                <b x-text="avgRating"></b>
                <span x-show="!isSuccess">({{ $cafe->reviews->count() }} reviews)</span>
                <span x-show="isSuccess">({{ $cafe->reviews->count() + 1 }} reviews)</span>
                <span
                    style="margin-left: auto; color: var(--color-earth-green); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    @if ($cafe->has_wifi)
                        <i class="bi bi-wifi"></i> WiFi Tersedia
                    @endif
                </span>
            </div>
            <p
                style="color: var(--color-text-muted); line-height: 1.7; font-size: 0.95rem; font-weight: 500; opacity: 0.9;">
                {{ $cafe->description }}
            </p>

            {{-- Interactive Rating --}}
            <div class="rating-section animate-up" style="animation-delay: 0.1s">
                <template x-if="!isSuccess">
                    <div>
                        <h4>Beri Rating Buat Cafe Ini!</h4>
                        <p style="font-size: 0.85rem; color: var(--color-text-muted);">Gimana menurut buanmu tempat ini?</p>
                        
                        <div class="rating-input">
                            <template x-for="i in 5">
                                <i class="bi" 
                                   :class="(hoverRating >= i || userRating >= i) ? 'bi-star-fill active' : 'bi-star'"
                                   @mouseenter="hoverRating = i"
                                   @mouseleave="hoverRating = 0"
                                   @click="userRating = i"></i>
                            </template>
                        </div>

                        <button @click="submitRating" 
                                class="btn btn-primary" 
                                style="width: 100%; padding: 14px; border-radius: 16px; font-weight: 700;"
                                :disabled="userRating === 0 || isSubmitting">
                            <span x-show="!isSubmitting">Kirim Rating</span>
                            <span x-show="isSubmitting"><i class="bi bi-arrow-repeat animate-spin"></i> Mengirim...</span>
                        </button>
                    </div>
                </template>

                <template x-if="isSuccess">
                    <div class="animate-up">
                        <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: var(--color-earth-green);"></i>
                        <h4 style="margin-top: 15px;">Mantap, Terimakasih!</h4>
                        <p style="font-size: 0.85rem; color: var(--color-text-muted);">Rating buanmu sudah masuk.</p>
                    </div>
                </template>
            </div>
            <div
                style="margin-top: 22px; font-size: 0.85rem; color: var(--color-text-muted); display: flex; gap: 8px; font-weight: 500;">
                <i class="bi bi-geo-alt-fill" style="color: var(--color-coffee);"></i>
                {{ $cafe->address }}
            </div>
        </div>

        <div class="action-grid animate-up" style="animation-delay: 0.1s">
            <a href="{{ $cafe->google_maps_url }}" target="_blank" class="btn btn-primary"
                style="background: var(--color-coffee-dark); font-size: 0.85rem; padding: 14px; border-radius: 18px;">
                <i class="bi bi-geo-alt"></i> Petunjuk
            </a>
            <a href="https://wa.me/{{ $cafe->whatsapp_number }}" target="_blank" class="btn btn-primary"
                style="background: #25D366; font-size: 0.85rem; padding: 14px; box-shadow: 0 8px 20px rgba(37, 211, 102, 0.2); border-radius: 18px;">
                <i class="bi bi-whatsapp"></i> WhatsApp
            </a>
        </div>

        <div class="menu-section animate-up" style="animation-delay: 0.2s">
            <div class="section-header">
                <h3>Menu Kami</h3>
            </div>

            <div class="menu-tabs">
                <template x-for="tab in activeTabs">
                    <div class="tab" :class="currentTab === tab ? 'active' : ''" @click="currentTab = tab"
                        x-text="tab.charAt(0).toUpperCase() + tab.slice(1).replace('-', ' ')">
                    </div>
                </template>
            </div>

            <div class="menu-list">
                @foreach ($cafe->menus as $menu)
                    <div class="menu-item animate-up" x-show="currentTab === '{{ $menu->type }}'"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <img src="{{ $menu->image_path ?? 'https://images.unsplash.com/photo-1541167760496-162955ed8a9f?auto=format&fit=crop&q=80&w=200' }}"
                            alt="{{ $menu->name }}" class="menu-img" loading="lazy">
                        <div class="menu-info">
                            <h4 class="line-clamp-1">{{ $menu->name }}</h4>
                            <div class="price">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                        </div>
                    </div>
                @endforeach

                @if ($cafe->menus->isEmpty())
                    <div style="text-align: center; padding: 50px 20px; color: var(--color-text-muted);">
                        <i class="bi bi-cup-hot"
                            style="font-size: 3rem; opacity: 0.1; margin-bottom: 15px; display: block;"></i>
                        <p style="font-size: 0.95rem; font-weight: 500;">Yah, menu belum tersedia nih.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection