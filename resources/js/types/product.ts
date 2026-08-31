export type ProductImage = {
    id: number;
    product_id: number;
    image_path: string;
    image_url: string;
    order: number;
    created_at: string;
    updated_at: string;
};

export type ProductSpecification = {
    id: number;
    product_id: number;
    icon_path: string;
    icon_url: string;
    question: string;
    answer: string;
    order: number;
    created_at: string;
    updated_at: string;
};

export type ProductExperienceItem = {
    id: number;
    product_id: number;
    icon_path: string;
    icon_url: string;
    title: string;
    order: number;
    created_at: string;
    updated_at: string;
};

export type NavProduct = {
    title: string;
    href: string;
    image_url: string | null;
};

export type Product = {
    id: number;
    slug: string;
    title_part1: string;
    title_part1_color: string;
    title_part2: string;
    title_part2_color: string;
    subtitle: string | null;
    subtitle_color: string;
    description: string | null;
    net_piece_weight: string | null;
    pieces_per_pack: string | null;
    shelf_life: string | null;
    storage: string | null;
    allergens: string | null;
    moq: string | null;
    spec_image_path: string | null;
    spec_image_url: string | null;
    technical_data_sheet_path: string | null;
    technical_data_sheet_url: string | null;
    pieces_per_pack_path: string | null;
    pieces_per_pack_url: string | null;
    experience_title: string | null;
    seo_title: string;
    images: ProductImage[];
    specifications: ProductSpecification[];
    experience_items: ProductExperienceItem[];
    created_at: string;
    updated_at: string;
};
