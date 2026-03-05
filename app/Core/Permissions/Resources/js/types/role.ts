import type { User } from '@/types';

export type Permission = {
    id: number;
    name: string;
    module: string;
    description: string;
    display_name?: string;
};

export type Role = {
    id: number;
    name: string;
    permissions: Permission[];
    users: User[];
    permissions_count?: number;
    users_count?: number;
};
