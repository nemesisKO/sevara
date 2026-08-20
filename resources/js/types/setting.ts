export type Setting = {
    id: number;
    catalogue_path: string | null;
    catalogue_url: string | null;
    logo_path: string | null;
    logo_url: string | null;
    created_at: string;
    updated_at: string;
};

export type SiteSettings = {
    catalogue_url: string | null;
    logo_url: string | null;
};

export type SeoDefaults = {
    url: string;
    name: string;
    title: string;
    description: string;
    image: string;
};
