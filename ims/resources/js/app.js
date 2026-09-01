import 'animate.css';
import { createIcons, icons } from 'lucide';

// Initialize Lucide icons on DOMContentLoaded and Livewire navigation
function initLucideIcons() {
    createIcons({ icons });
}

document.addEventListener('DOMContentLoaded', initLucideIcons);
document.addEventListener('livewire:navigated', initLucideIcons);
document.addEventListener('livewire:load', initLucideIcons);

// Global Alpine helpers or utility functions
window.initLucideIcons = initLucideIcons;
