import { NavItem, User } from '@/types';
import {
    Blocks,
    ChartNoAxesCombined,
    Gamepad2,
    Home,
    LayoutDashboard,
    MessageCircle,
    MessageCircleWarning,
    Plus,
    Rss,
    Star,
    UserIcon,
} from 'lucide-vue-next';
import { onBeforeMount } from 'vue';

// TODO: Move this to back-end

export function getNavigationElements(user: User): { [key: string]: NavItem[] } {
    const items = {
        app: [
            {
                title: 'Home',
                href: route('home'),
                icon: Home,
            },
        ],
        games: [
            {
                title: 'List of games',
                href: route('games.index'),
                icon: Gamepad2,
            },
        ],
        discussions: [
            {
                title: 'List of discussions',
                href: route('discussions.index'),
                icon: Rss,
            },
        ],
        genres: [
            {
                title: 'List of genres',
                href: route('genres.index'),
                icon: Blocks,
            },
        ],
    };

    onBeforeMount(() => {
        if (user.role !== 'guest') {
            items['app'].push({
                title: 'My reports',
                href: route('user.reports'),
                icon: MessageCircleWarning,
            });
        }
        if (['moderator', 'admin', 'developer'].includes(user.role)) {
            items['app'].push({
                title: 'Dashboard',
                href: route('admin.dashboard'),
                icon: LayoutDashboard,
            });
        }

        if (['game_creator', 'moderator', 'admin', 'developer'].includes(user.role)) {
            items['games'].push({
                title: 'Add new game',
                href: route('games.create'),
                icon: Plus,
            });
        }
    });

    return items;
}

export function getAdminNavigationElements(): NavItem[] {
    return [
        {
            title: 'Dashboard',
            href: route('admin.dashboard'),
            icon: ChartNoAxesCombined,
        },
        {
            title: 'Games',
            href: '',
            icon: Gamepad2,
        },
        {
            title: 'Reviews',
            href: '',
            icon: Star,
        },
        {
            title: 'Discussions',
            href: '',
            icon: Rss,
        },
        {
            title: 'Comments',
            href: '',
            icon: MessageCircle,
        },
        {
            title: 'Genres',
            href: '',
            icon: Blocks,
        },
        {
            title: 'Users',
            href: '',
            icon: UserIcon,
        },
        {
            title: 'Reports',
            href: '',
            icon: MessageCircleWarning,
        },
    ];
}
