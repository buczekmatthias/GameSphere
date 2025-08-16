import { Pagination } from '@/types';

export function getPaginationData(pagination: Pagination): Pagination {
    return {
        meta: {
            current_page: pagination.meta.current_page,
            from: pagination.meta.from,
            last_page: pagination.meta.last_page,
            per_page: pagination.meta.per_page,
            to: pagination.meta.to,
            total: pagination.meta.total,
        },
    };
}
