export type SlideTextStyle = {
    top?: number | string | null;
    left?: number | string | null;
    right?: number | string | null;
    bottom?: number | string | null;
    size?: number | string | null;
    weight?: number | string | null;
    color?: string | null;
};

export type Slide = {
    id: number;
    image_path: string;
    image_url: string;
    mobile_image_path: string | null;
    mobile_image_url: string;
    link: string;
    title: string | null;
    description: string | null;
    title_style: SlideTextStyle | null;
    description_style: SlideTextStyle | null;
    primary_cta_text: string | null;
    primary_cta_link: string | null;
    secondary_cta_text: string | null;
    secondary_cta_link: string | null;
    created_at: string;
    updated_at: string;
};
