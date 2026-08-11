export type PrivateLabelSection = 'highlight' | 'step' | 'feature';

export type PrivateLabelItem = {
    id: number;
    section: PrivateLabelSection;
    icon_path: string | null;
    icon_url: string | null;
    title: string;
    description: string;
    order: number;
    created_at: string;
    updated_at: string;
};

export type PackagingFormat = {
    id: number;
    name: string;
    description: string | null;
    image_path: string | null;
    image_url: string | null;
    is_featured: boolean;
    order: number;
    created_at: string;
    updated_at: string;
};

export type PrivateLabelInquiry = {
    id: number;
    full_name: string;
    company_name: string;
    country: string;
    product_format: string;
    estimated_quantity: string;
    message: string | null;
    read_at: string | null;
    created_at: string;
    updated_at: string;
};

export type PrivateLabelAdminSection = {
    value: PrivateLabelSection;
    label: string;
    description: string;
    items: PrivateLabelItem[];
};
