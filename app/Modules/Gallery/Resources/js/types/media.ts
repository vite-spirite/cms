import type { User } from '@/types';

export interface Media {
    id: number;
    label: string;
    path: string;
    url: string;
    created_at: string;
    uploader_id: number;
    uploader?: User;
}
