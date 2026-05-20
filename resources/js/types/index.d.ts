import { AxiosInstance } from "axios";

declare global {
    interface Window {
        axios: AxiosInstance;
    }
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string | null;
    created_at: string;
    updated_at: string;
}

export type ReportType =
    | "rental_living"
    | "rental_business"
    | "buying_living"
    | "buying_business";
export type ReportStatus =
    | "not_accessible"
    | "awaiting_payment"
    | "payment_processing"
    | "payment_cancelled"
    | "payment_failed"
    | "pending"
    | "to_be_sent"
    | "sent"
    | "error";

export interface Report {
    id: number;
    report_type: ReportType;
    url: string;
    email: string | null;
    status: ReportStatus;
    report_url: string | null;
    page_token: string | null;
    error_message: string | null;
    processed_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface ContactInquiry {
    id: number;
    name: string;
    email: string;
    subject: string;
    message: string;
    locale: string;
    created_at: string;
    updated_at: string;
}

export interface Settings {
    rental_living_ro: string;
    rental_living_eng: string;
    buying_living_ro: string;
    buying_living_eng: string;
    auto_send: boolean;
    [key: string]: string | boolean;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface FlashMessages {
    success?: string;
    error?: string;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
    flash: FlashMessages;
    locale: string;
    supportedLocales: string[];
    domainUrls: Record<string, string>;
    localizedUrls: Record<string, string>;
    seoIndexing: boolean;
    seo: {
        canonical?: string | null;
        alternates?: Record<string, string | null>;
        xDefault?: string | null;
    };
    translations: Record<string, string>;
    [key: string]: unknown;
}
