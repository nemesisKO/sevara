export type SocialIconName =
    | 'instagram'
    | 'linkedin'
    | 'facebook'
    | 'youtube'
    | 'x'
    | 'tiktok'
    | 'whatsapp'
    | 'website';

export type SocialLink = {
    label: string;
    icon: SocialIconName;
    url: string;
};

export type Setting = {
    id: number;
    catalogue_path: string | null;
    catalogue_url: string | null;
    logo_path: string | null;
    logo_url: string | null;
    social_links: SocialLink[] | null;
    created_at: string;
    updated_at: string;
};

export type SiteSettings = {
    catalogue_url: string | null;
    logo_url: string | null;
    social_links: SocialLink[];
};

export type SeoDefaults = {
    url: string;
    name: string;
    title: string;
    description: string;
    image: string;
};
