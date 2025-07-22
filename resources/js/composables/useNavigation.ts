import { NavItem, User } from '@/types';
import { Blocks, Gamepad2, Home, MessageCircle, MessageCircleWarning, Plus } from 'lucide-vue-next';
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
                icon: MessageCircle,
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
