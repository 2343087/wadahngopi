@extends('layouts.app')

@section('title', 'Profil - WadahNgopi.Com')

@section('content')
    <style>
        /* ===== Profile Header ===== */
        .profile-header {
            padding: 60px 20px 40px;
            text-align: center;
            position: relative;
        }

        .profile-header::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top, rgba(111, 78, 55, 0.12), transparent 60%);
            z-index: -1;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(145deg, var(--color-coffee-dark), var(--color-coffee));
            color: white;
            border-radius: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 22px;
            font-size: 3.5rem;
            box-shadow:
                0 20px 40px rgba(62, 39, 35, 0.25),
                inset 0 0 0 1px rgba(255, 255, 255, 0.15);
            animation: floatAvatar 5s ease-in-out infinite;
        }

        @keyframes floatAvatar {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        .profile-info h1 {
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--color-coffee-dark);
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .profile-info p {
            color: var(--color-text-muted);
            font-weight: 500;
            font-size: 0.95rem;
            opacity: 0.85;
        }

        /* ===== Info Card ===== */
        .info-card {
            margin: 22px;
            padding: 32px;
            border-radius: 34px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(14px);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }

        .info-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg,
                    rgba(255, 255, 255, 0.25),
                    rgba(255, 255, 255, 0.05),
                    rgba(255, 255, 255, 0.25));
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .info-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 50px rgba(62, 39, 35, 0.18);
        }

        .info-card:hover::before {
            opacity: 1;
        }

        .info-card h3 {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: 14px;
            color: var(--color-coffee-dark);
            letter-spacing: -0.3px;
        }

        .info-card p {
            font-size: 0.95rem;
            line-height: 1.75;
            opacity: 0.85;
        }

        /* ===== Admin CTA ===== */
        .admin-cta {
            margin: 0 22px;
        }

        .admin-cta .btn {
            border-radius: 18px;
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 14px 30px rgba(62, 39, 35, 0.25);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .admin-cta .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 45px rgba(62, 39, 35, 0.35);
        }

        .admin-cta i {
            margin-right: 6px;
        }

        /* ===== Version Label ===== */
        .version-label {
            text-align: center;
            padding: 42px 0 20px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--color-text-muted);
            letter-spacing: 1.6px;
            opacity: 0.7;
        }
    </style>

    <div class="animate-up">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="bi bi-person-stars"></i>
            </div>
            <div class="profile-info">
                <h1>Halo, Buan Kopi Mania!</h1>
                <p>Pecinta Kopi Sejati</p>
            </div>
        </div>

        <div class="glass info-card">
            <h3>Tentang WadahNgopi</h3>
            <p>
                WadahNgopi.Com adalah aplikasi pencarian tempat ngopi Kalimantan kekinian yang ringan dan modern.
                Dirancang buat buanmu yang pengen eksplor spot ngopi baru
                dan pengalaman visual yang clean.
            </p>
        </div>

        <div class="admin-cta animate-up" style="animation-delay: 0.1s">
            <a href="/admin" class="btn btn-primary" style="width: 100%; padding: 18px; border-radius: 20px;">
                <i class="bi bi-shield-lock-fill"></i>
                Login Panel Admin
            </a>
        </div>

        <div class="version-label">
            WADAHNGOPI.COM V1.0
        </div>
    </div>
@endsection