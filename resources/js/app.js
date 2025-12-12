// Import only the icons you actually use instead of all icons
import { 
    createIcons,
    Home,
    LayoutGrid,
    FolderGit2,
    ChevronsUpDown,
    BookOpenText,
    Settings,
    User,
    Bell,
    Search,
    Plus,
    Check,
    X,
    ChevronRight,
    ChevronLeft,
    Calendar,
    Clock,
    Flame,
    Zap,
    Target,
    TrendingUp,
    Award,
    Gift,
    Edit,
    Trash2,
    MoreVertical
} from 'lucide';

// Import SortableJS
import Sortable from 'sortablejs';

// Make Sortable available globally
window.Sortable = Sortable;

// Import TipTap editor (exposes window.createTiptapEditor)
import './tiptap-editor.js';

// Import Pomodoro store
import pomodoroStore from './pomodoro-store.js';

// Register Alpine.js store and components
document.addEventListener('alpine:init', () => {
    const store = pomodoroStore();
    Alpine.store('pomodoro', store);
    
    // Initialize the store
    if (store.init) {
        store.init();
    }
});

// Initialize Lucide icons with only the ones we need
const iconSet = {
    Home,
    LayoutGrid,
    FolderGit2,
    ChevronsUpDown,
    BookOpenText,
    Settings,
    User,
    Bell,
    Search,
    Plus,
    Check,
    X,
    ChevronRight,
    ChevronLeft,
    Calendar,
    Clock,
    Flame,
    Zap,
    Target,
    TrendingUp,
    Award,
    Gift,
    Edit,
    Trash2,
    MoreVertical
};

document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons: iconSet });
});

// Re-initialize icons after Livewire updates
document.addEventListener('livewire:navigated', () => {
    createIcons({ icons: iconSet });
});
