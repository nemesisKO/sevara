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

export type InquiryProduct = {
    id: number;
    name: string;
};

export type PrivateLabelInquiry = {
    id: number;
    full_name: string;
    company_name: string | null;
    email: string;
    phone: string | null;
    intent: string | null;
    source_page: string | null;
    country: string;
    product_format: string;
    product_name: string | null;
    estimated_quantity: string;
    message: string | null;
    read_at: string | null;
    project_url: string | null;
    project_exists: boolean;
    created_at: string;
    updated_at: string;
};

export type PrivateLabelProjectFile = {
    name: string;
    path: string;
    size: number;
};

export type PrivateLabelProject = {
    id: number;
    reference: string;
    full_name: string;
    company_name: string;
    email: string;
    details: Record<string, unknown>;
    files: Record<string, PrivateLabelProjectFile[]> | null;
    consented_at: string;
    created_at: string;
};

export type PrivateLabelAdminSection = {
    value: PrivateLabelSection;
    label: string;
    description: string;
    items: PrivateLabelItem[];
};
