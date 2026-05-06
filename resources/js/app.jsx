import './bootstrap';
import React, { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import DockNav from './components/DockNav';
import WfcScoreSection from './components/WfcScoreSection';
import VibeMeter from './components/VibeMeter';
import CheckInButton from './components/CheckInButton';
import BadgeShowcase from './components/BadgeShowcase';

const components = {
    'bottom-nav-react': DockNav,
    'wfc-score-react': WfcScoreSection,
    'vibe-meter-react': VibeMeter,
    'check-in-react': CheckInButton,
    'badge-showcase-react': BadgeShowcase,
};

document.addEventListener('DOMContentLoaded', () => {
    Object.entries(components).forEach(([id, Component]) => {
        const rootElement = document.getElementById(id);
        if (rootElement) {
            const props = Object.keys(rootElement.dataset).reduce((acc, key) => {
                try {
                    acc[key] = JSON.parse(rootElement.dataset[key]);
                } catch (e) {
                    acc[key] = rootElement.dataset[key];
                }
                return acc;
            }, {});

            const root = createRoot(rootElement);
            root.render(
                <StrictMode>
                    <Component {...props} />
                </StrictMode>
            );
        }
    });
});

// Re-initialize React on Livewire navigation
document.addEventListener('livewire:navigated', () => {
    Object.entries(components).forEach(([id, Component]) => {
        const rootElement = document.getElementById(id);
        if (rootElement) {
            const props = Object.keys(rootElement.dataset).reduce((acc, key) => {
                try {
                    acc[key] = JSON.parse(rootElement.dataset[key]);
                } catch (e) {
                    acc[key] = rootElement.dataset[key];
                }
                return acc;
            }, {});

            const root = createRoot(rootElement);
            root.render(
                <StrictMode>
                    <Component {...props} />
                </StrictMode>
            );
        }
    });
});
