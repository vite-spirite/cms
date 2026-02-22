import type { User } from '@/types';

export type LogLevel = 'debug' | 'info' | 'warning' | 'error' | 'success';

export interface LogCount {
    level: LogLevel;
    count: number;
}

export interface Log {
    id: number;
    level: LogLevel;
    action: string;
    category: string;
    message: string;
    user?: User;
    subject: any;
    url: string;
    ip_address: string;
    user_agent: string;
    created_at: string;
    context: any;
}
