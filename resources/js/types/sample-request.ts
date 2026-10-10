export type SampleRequest = {
    id: number;
    product_id: number | null;
    product_name: string;
    full_name: string;
    email: string;
    company_name: string | null;
    phone: string | null;
    sample_volume: string;
    shipping_term: 'FOB' | 'CIF';
    description: string | null;
    read_at: string | null;
    created_at: string;
};
