export type PageStatus = 'draft' | 'published' | 'archived';

export interface Block {
    icon: string;
    label: string;
    type: string;
    schema: Record<string, BlockSchema>;
}

export interface BlockSchema {
    type: string;
    label: string;
    default?: string;
    required?: boolean;
    options?: string[];
}

export interface PageBlock {
    id: string;
    type: string;
    order: number;
    data: Record<string, any>;
}

export interface Page {
    id: number;
    title: string;
    slug: string;
    content: PageBlock[];
    status: PageStatus;
    og_balises: Record<string, string>;
    created_at: string;
    updated_at: string;
}

export interface Definition {
    type: string;
    icon: string;
    label: string;
    schema: Record<string, any>;
}

export type PageBuilderSettings = Omit<Page, 'id' | 'updated_at' | 'created_at' | 'content'>;
