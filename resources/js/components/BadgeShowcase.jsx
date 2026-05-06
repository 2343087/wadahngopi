import React, { useState, useEffect } from 'react';
import { motion } from 'motion/react';

const BadgeShowcase = () => {
    const [badges, setBadges] = useState([]);
    const [stats, setStats] = useState({ totalCheckIns: 0, uniqueCafes: 0 });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchBadges();
    }, []);

    const fetchBadges = async () => {
        try {
            const res = await fetch('/api/badges', {
                headers: { 'Accept': 'application/json' }
            });
            if (res.ok) {
                const data = await res.json();
                setBadges(data.badges || []);
                setStats({
                    totalCheckIns: data.total_check_ins || 0,
                    uniqueCafes: data.unique_cafes || 0,
                });
            }
        } catch (e) {
            console.error('[Badges] Fetch error:', e);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <div className="space-y-4 p-6">
                {[1,2,3].map(i => (
                    <div key={i} className="h-20 bg-slate-100 rounded-2xl animate-pulse"></div>
                ))}
            </div>
        );
    }

    const earned = badges.filter(b => b.earned);
    const locked = badges.filter(b => !b.earned);

    return (
        <div className="min-h-screen" style={{
            background: 'radial-gradient(ellipse 80% 60% at 18% -8%, rgba(234,179,8,0.1) 0%, transparent 58%), radial-gradient(ellipse 100% 100% at 50% 50%, #FFFBF0 0%, #FEF3C7 100%)'
        }}>
            {/* Header */}
            <header className="sticky top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100 px-6 py-4">
                <div className="flex items-center gap-3">
                    <a href="/information" className="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-200 transition-all active:scale-90">
                        <i className="ph-bold ph-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h1 className="text-lg font-black text-[#2C1810]">Badge Collection</h1>
                        <p className="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Cafe Hopping Achievements</p>
                    </div>
                </div>
            </header>

            {/* Stats Banner */}
            <div className="px-6 pt-6 pb-4">
                <div className="bg-gradient-to-br from-[#1A0F0A] to-[#2D1B12] rounded-[28px] p-6 text-white relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl -mr-20 -mt-20"></div>
                    <div className="relative z-10 flex items-center gap-6">
                        <div className="text-center">
                            <p className="text-3xl font-black text-amber-500">{stats.uniqueCafes}</p>
                            <p className="text-[0.55rem] font-bold text-white/40 uppercase tracking-widest mt-1">Cafe Dikunjungi</p>
                        </div>
                        <div className="w-px h-12 bg-white/10"></div>
                        <div className="text-center">
                            <p className="text-3xl font-black text-white">{stats.totalCheckIns}</p>
                            <p className="text-[0.55rem] font-bold text-white/40 uppercase tracking-widest mt-1">Total Check-in</p>
                        </div>
                        <div className="w-px h-12 bg-white/10"></div>
                        <div className="text-center">
                            <p className="text-3xl font-black text-emerald-400">{earned.length}</p>
                            <p className="text-[0.55rem] font-bold text-white/40 uppercase tracking-widest mt-1">Badge Earned</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Earned Badges */}
            {earned.length > 0 && (
                <div className="px-6 mb-6">
                    <h2 className="text-sm font-bold text-[#2C1810] uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i className="ph-fill ph-trophy text-amber-600"></i>
                        Unlocked
                    </h2>
                    <div className="grid grid-cols-2 gap-3">
                        {earned.map((badge, i) => (
                            <motion.div
                                key={badge.slug}
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ delay: i * 0.1 }}
                                className="bg-white border border-amber-100 rounded-[24px] p-5 text-center shadow-sm hover:shadow-md transition-all"
                            >
                                <span className="text-4xl block mb-2">{badge.icon}</span>
                                <h3 className="text-sm font-black text-[#2C1810] mb-1">{badge.name}</h3>
                                <p className="text-[0.6rem] text-slate-400 font-medium leading-snug">{badge.description}</p>
                            </motion.div>
                        ))}
                    </div>
                </div>
            )}

            {/* Locked Badges */}
            {locked.length > 0 && (
                <div className="px-6 pb-32">
                    <h2 className="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i className="ph-fill ph-lock-simple text-slate-300"></i>
                        Locked
                    </h2>
                    <div className="grid grid-cols-2 gap-3">
                        {locked.map((badge, i) => (
                            <motion.div
                                key={badge.slug}
                                initial={{ opacity: 0, y: 20 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ delay: i * 0.1 + 0.3 }}
                                className="bg-slate-50/80 border border-slate-100 rounded-[24px] p-5 text-center relative overflow-hidden"
                            >
                                <div className="opacity-30 grayscale">
                                    <span className="text-4xl block mb-2">{badge.icon}</span>
                                </div>
                                <h3 className="text-sm font-bold text-slate-400 mb-1">{badge.name}</h3>
                                <p className="text-[0.6rem] text-slate-300 font-medium leading-snug mb-3">{badge.description}</p>
                                {/* Progress bar */}
                                <div className="w-full h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                    <motion.div 
                                        className="h-full bg-amber-400 rounded-full"
                                        initial={{ width: 0 }}
                                        animate={{ width: `${(badge.progress / badge.target) * 100}%` }}
                                        transition={{ duration: 1, delay: 0.5 }}
                                    />
                                </div>
                                <span className="text-[0.55rem] font-bold text-slate-400 mt-1.5 block">{badge.progress}/{badge.target}</span>
                            </motion.div>
                        ))}
                    </div>
                </div>
            )}

            {/* Empty State */}
            {badges.length === 0 && (
                <div className="px-6 py-20 text-center">
                    <span className="text-5xl block mb-4">🎯</span>
                    <h3 className="text-lg font-black text-[#2C1810] mb-2">Belum Ada Badge</h3>
                    <p className="text-sm text-slate-400 mb-6">Check-in di cafe untuk mulai kumpulkan badge!</p>
                    <a href="/explore" className="inline-flex items-center gap-2 bg-amber-500 text-white px-6 py-3 rounded-2xl font-bold text-sm">
                        <i className="ph-fill ph-compass"></i>
                        Jelajahi Cafe
                    </a>
                </div>
            )}
        </div>
    );
};

export default BadgeShowcase;
