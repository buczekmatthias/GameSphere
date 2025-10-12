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
        users: [
            {
                title: 'List of users',
                href: route('users.index'),
                icon: UserIcon,
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
            href: route('admin.games.index'),
            icon: Gamepad2,
            group: 'admin.games',
        },
        {
            title: 'Reviews',
            href: route('admin.reviews.index'),
            icon: Star,
            group: 'admin.reviews',
        },
        {
            title: 'Discussions',
            href: route('admin.discussions.index'),
            icon: Rss,
            group: 'admin.discussions',
        },
        {
            title: 'Comments',
            href: route('admin.comments.index'),
            icon: MessageCircle,
            group: 'admin.comments',
        },
        {
            title: 'Genres',
            href: route('admin.genres.index'),
            icon: Blocks,
            group: 'admin.genres',
        },
        {
            title: 'Users',
            href: route('admin.users.index'),
            icon: UserIcon,
            group: 'admin.users',
        },
        {
            title: 'Reports',
            href: route('admin.reports.index'),
            icon: MessageCircleWarning,
            group: 'admin.reports',
        },
    ];
}
