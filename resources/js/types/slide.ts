export type Slide = {
    id: number;
    image_path: string;
    image_url: string;
    link: string;
    primary_cta_text: string | null;
    primary_cta_link: string | null;
    secondary_cta_text: string | null;
    secondary_cta_link: string | null;
    created_at: string;
    updated_at: string;
};
