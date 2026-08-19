import React, { useRef, useState, useEffect } from 'react';
import { motion, useMotionValue, useSpring, useTransform, AnimatePresence } from 'motion/react';

const DockItem = ({ icon, label, url, active, mouseX, isMobile, onClick }) => {
    const ref = useRef(null);

    // Snappy Physics
    const springConfig = { mass: 0.1, stiffness: 220, damping: 20 };
    
    // Magnification Logic
    const distance = useTransform(mouseX, (val) => {
        const bounds = ref.current?.getBoundingClientRect() ?? { x: 0, width: 0 };
        return val - bounds.x - bounds.width / 2;
    });

    // Proportional Dimensions
    const baseSize = isMobile ? 42 : 46;
    const magnifiedSize = isMobile ? 58 : 68;
    
    const sizeSync = useTransform(distance, [-100, 0, 100], [baseSize, magnifiedSize, baseSize]);
    const size = useSpring(sizeSync, springConfig);

    const iconSizeSync = useTransform(distance, [-100, 0, 100], [isMobile ? 18 : 20, isMobile ? 24 : 30, isMobile ? 18 : 20]);
    const iconSize = useSpring(iconSizeSync, springConfig);

    // Tooltip Visibility Logic
    const tooltipOpacity = useTransform(distance, [-40, 0, 40], [0, 1, 0]);
    const tooltipY = useTransform(distance, [-40, 0, 40], [0, -10, 0]);
    const tooltipX = useTransform(distance, [-40, 0, 40], [0, 10, 0]);


    const handleClick = (e) => {
        if (onClick) {
            onClick(e);
            return;
        }

        if (window.Livewire) {
            e.preventDefault();
            window.hapticFeedback?.('light');
            window.Livewire.navigate(url);
        }
    };

    return (
        <div className="relative flex flex-col items-center justify-center">
            {/* Tooltip Label (Desktop/Touch only) */}
            <motion.div
                style={{ 
                    opacity: isMobile ? 0 : tooltipOpacity,
                    y: isMobile ? tooltipY : 0,
                    x: isMobile ? 0 : tooltipX,
                    scale: useTransform(tooltipOpacity, [0, 1], [0.8, 1])
                }}
                className={`absolute z-[10001] px-3 py-1 rounded-lg bg-espresso border border-amber-500/30 shadow-2xl pointer-events-none flex items-center justify-center ${
                    isMobile ? '-top-12' : 'left-full ml-2'
                }`}
            >
                <span className="text-[10px] font-black text-amber-400 uppercase tracking-[0.2em] whitespace-nowrap">
                    {label}
                </span>
                <div className={`absolute w-2 h-2 bg-espresso rotate-45 border-amber-500/30 ${
                    isMobile ? '-bottom-1 left-1/2 -translate-x-1/2 border-r border-b' : '-left-1 top-1/2 -translate-y-1/2 border-l border-b'
                }`}></div>
            </motion.div>

            <motion.a
                ref={ref}
                href={url}
                onClick={handleClick}
                aria-label={label} /* tambah aria-label buat aksesibilitas ngab */
                style={{ width: size, height: size }}
                className={`relative flex items-center justify-center rounded-2xl transition-all duration-300 ${
                    active 
                        ? 'bg-amber-500 text-espresso shadow-[0_0_20px_rgba(234,179,8,0.5)] ring-2 ring-amber-400/50 ring-offset-2 ring-offset-espresso' 
                        : 'bg-white/10 text-white/60 hover:text-amber-400 border border-white/10'
                }`}
            >
                <div className="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent opacity-10 pointer-events-none"></div>
                
                <motion.i 
                    style={{ fontSize: iconSize }}
                    className={`${active ? icon.replace('ph ', 'ph-fill ') : icon} z-10 transition-colors duration-200`}
                ></motion.i>
            </motion.a>

            {/* Active Glow Indicator */}
            {active && (
                <motion.div 
                    layoutId="active-indicator"
                    className={`absolute bg-amber-500 rounded-full shadow-[0_0_12px_#EAB308] ${
                        isMobile ? '-bottom-2.5 w-5 h-1 left-1/2 -translate-x-1/2' : '-left-2.5 h-5 w-1 top-1/2 -translate-y-1/2'
                    }`}
                />
            )}
        </div>
    );
};

const DockNav = ({ currentRoute, routes, isAuthenticated, logoutUrl, csrfToken }) => {
    const mouseX = useMotionValue(Infinity);
    const [isMobile, setIsMobile] = useState(false);

    useEffect(() => {
        const checkMobile = () => setIsMobile(window.innerWidth < 768);
        checkMobile();
        window.addEventListener('resize', checkMobile);
        return () => window.removeEventListener('resize', checkMobile);
    }, []);

    const handleLogout = (e) => {
        e.preventDefault();
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = logoutUrl;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    };

    const items = [
        { id: 'home', label: 'Beranda', icon: 'ph ph-house', url: routes.home },
        { id: 'roastery', label: 'Roastery', icon: 'ph ph-coffee-bean', url: routes.roastery },
        { id: 'explore', label: 'Explore', icon: 'ph ph-compass', url: routes.explore },
        { id: 'saved', label: 'Saved', icon: 'ph ph-bookmark-simple', url: routes.saved },
    ];

    // Add Auth Item
    if (isAuthenticated === 'true') {
        items.push({ 
            id: 'logout', 
            label: 'Keluar', 
            icon: 'ph ph-sign-out', 
            url: '#', 
            onClick: handleLogout 
        });
    } else {
        items.push({ 
            id: 'login', 
            label: 'Masuk', 
            icon: 'ph ph-user-circle', 
            url: '/admin/login' 
        });
    }

    // Helper for touch interactions
    const handleTouch = (e) => {
        const touch = e.touches[0];
        if (touch) {
            mouseX.set(touch.pageX);
        }
    };

    return (
        <div className="fixed bottom-8 sm:bottom-10 md:bottom-auto md:top-1/2 md:-translate-y-1/2 left-1/2 -translate-x-1/2 md:translate-x-0 md:left-8 z-[10000] pointer-events-none px-4 w-full md:w-auto flex justify-center md:flex-col">
            <motion.nav
                onMouseMove={(e) => mouseX.set(isMobile ? e.pageX : e.pageY)}
                onMouseLeave={() => mouseX.set(Infinity)}
                onTouchMove={handleTouch}
                onTouchEnd={() => mouseX.set(Infinity)}
                className="pointer-events-auto flex flex-row items-end md:flex-col md:items-center gap-2.5 sm:gap-4 md:gap-6 px-4 sm:px-6 md:px-3.5 py-3.5 md:py-8 rounded-[28px] bg-espresso/95 backdrop-blur-3xl border border-white/10 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.7)]"
                style={{ 
                    height: isMobile ? 72 : 'auto',
                    width: !isMobile ? 78 : 'auto',
                    maxWidth: 'fit-content'
                }}
            >
                {items.map((item) => (
                    <DockItem 
                        key={item.id} 
                        {...item} 
                        active={currentRoute === item.id || (item.id === 'information' && currentRoute.startsWith('information'))}
                        mouseX={mouseX} 
                        isMobile={isMobile}
                    />
                ))}
            </motion.nav>
        </div>
    );
};

export default DockNav;
