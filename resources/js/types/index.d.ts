export interface User {
    id: number;
    name: string;
    email: string;
    role: 'super_admin' | 'admin_rt' | 'admin_karang_taruna';
    organization_id: number | null;
    phone?: string | null;
    is_active?: boolean;
}

export interface Organization {
    id: number;
    name: string;
    slug: string;
    type: 'rt' | 'karang_taruna';
    code?: string | null;
    description?: string | null;
    address?: string | null;
    phone?: string | null;
    email?: string | null;
    logo?: string | null;
    cover_image?: string | null;
    social_links?: Record<string, string> | null;
    active_period?: OrganizationPeriod | null;
    periods?: OrganizationPeriod[];
    positions?: OrganizationPosition[];
}

export interface OrganizationPeriod {
    id: number;
    organization_id: number;
    name: string;
    start_year: number;
    end_year: number;
    is_active: boolean;
    notes?: string | null;
    member_positions_count?: number;
    member_positions?: PositionMemberPeriod[];
}

export interface OrganizationPosition {
    id: number;
    organization_id: number;
    name: string;
    slug: string;
    max_members: number;
    sort_order: number;
    is_default: boolean;
    member_positions_count?: number;
}

export interface OrganizationMember {
    id: number;
    user_id?: number | null;
    organization_id: number;
    name: string;
    gender?: 'male' | 'female' | null;
    phone?: string | null;
    email?: string | null;
    avatar?: string | null;
    bio?: string | null;
    join_year?: number | null;
    status: 'active' | 'inactive' | 'alumni';
    user?: User | null;
    positions?: PositionMemberPeriod[];
}

export interface PositionMemberPeriod {
    id: number;
    organization_member_id: number;
    organization_position_id: number;
    organization_period_id: number;
    notes?: string | null;
    member?: OrganizationMember;
    position?: OrganizationPosition;
    period?: OrganizationPeriod;
}

export interface CashAccount {
    id: number;
    organization_id: number;
    name: string;
    account_number?: string | null;
    current_balance: number;
    description?: string | null;
    is_active: boolean;
    organization?: Organization;
}

export interface CashCategory {
    id: number;
    organization_id: number;
    name: string;
    type: 'income' | 'expense';
    description?: string | null;
    is_active: boolean;
    transactions_count?: number;
}

export interface CashTransaction {
    id: number;
    cash_account_id: number;
    cash_category_id: number;
    type: 'income' | 'expense';
    amount: number;
    transaction_date: string;
    description: string;
    reference_number?: string | null;
    attachment_path?: string | null;
    status: 'confirmed' | 'void';
    created_by_user_id?: number | null;
    account?: CashAccount;
    category?: CashCategory;
    creator?: User;
}

export interface MediaItem {
    id: number;
    disk: string;
    file_path: string;
    original_name: string;
    file_name: string;
    mime_type: string;
    file_size: number;
    width?: number | null;
    height?: number | null;
    alt_text?: string | null;
    caption?: string | null;
    url: string;
    uploader?: User;
    created_at?: string;
}

export interface ContentItem {
    id: number;
    organization_id: number;
    type: 'announcement' | 'article' | 'event' | 'activity' | 'page';
    title: string;
    slug: string;
    excerpt?: string | null;
    content?: any;
    content_html?: string | null;
    thumbnail?: string | null;
    status: 'draft' | 'published' | 'archived';
    published_at?: string | null;
    author_id?: number | null;
    view_count: number;
    is_featured: boolean;
    author?: User;
    organization?: Organization;
    event?: EventItem;
    activity?: ActivityItem;
    attachments?: ContentAttachmentItem[];
    updated_at?: string;
}

export interface EventItem {
    id: number;
    content_id: number;
    start_at: string;
    end_at?: string | null;
    location: string;
    maps_url?: string | null;
    is_online: boolean;
}

export interface ActivityItem {
    id: number;
    content_id: number;
    activity_date: string;
    location?: string | null;
    gallery?: ActivityMediaItem[];
}

export interface ActivityMediaItem {
    id: number;
    activity_id: number;
    media_id: number;
    sort_order: number;
    media?: MediaItem;
}

export interface ContentAttachmentItem {
    id: number;
    content_id: number;
    media_id: number;
    display_name: string;
    sort_order: number;
    media?: MediaItem;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    prev_page_url?: string | null;
    next_page_url?: string | null;
    links: { url: string | null; label: string; active: boolean }[];
}

export interface SharedProps {
    appName: string;
    settings: Record<string, string>;
    auth: {
        user: User | null;
    };
    organizations: Organization[];
    flash: {
        success?: string | null;
        error?: string | null;
        message?: string | null;
    };
}
