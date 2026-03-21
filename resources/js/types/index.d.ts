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

export type ReportType = "purchase" | "rental" | "commercial";
export type ReportStatus =
    | "not_accessible"
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

export interface Settings {
    id: number;
    purchase_prompt: string;
    rental_prompt: string;
    commercial_prompt: string;
    auto_send: boolean;
    created_at: string;
    updated_at: string;
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
    [key: string]: unknown;
}
