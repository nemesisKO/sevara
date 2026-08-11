export type BlogCategory = {
    id: number;
    name: string;
    slug: string;
    blogs_count?: number;
    created_at: string;
    updated_at: string;
};

export type Tag = {
    id: number;
    title: string;
    created_at: string;
    updated_at: string;
};

export type BlogBlockTableCell = {
    text: string;
    link_url: string;
};

export type BlogBlockType = 'article' | 'image' | 'hint' | 'table';

export type BlogBlock = {
    id: number;
    blog_id: number;
    type: BlogBlockType;
    order: number;
    title: string | null;
    body: string | null;
    image_path: string | null;
    image_url: string | null;
    link_text: string | null;
    link_url: string | null;
    table_data: BlogBlockTableCell[][] | null;
    created_at: string;
    updated_at: string;
};

export type BlogFaq = {
    question: string;
    answer: string;
};

export type Blog = {
    id: number;
    blog_category_id: number;
    title: string;
    slug: string;
    author: string | null;
    source_link: string | null;
    custom_date: string | null;
    read_duration: string | null;
    mini_review: string | null;
    meta_description: string | null;
    image_path: string | null;
    image_url: string | null;
    references: string[] | null;
    faqs: BlogFaq[] | null;
    category?: BlogCategory;
    blocks: BlogBlock[];
    tags: Tag[];
    created_at: string;
    updated_at: string;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    total: number;
};
