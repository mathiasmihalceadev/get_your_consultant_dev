import { AxiosInstance } from "axios";

declare global {
    interface Window {
        axios: AxiosInstance;
        dataLayer?: Record<string, unknown>[];
    }
}

export interface DataLayerEvent {
    event: string;
    event_id?: string;
    [key: string]: unknown;
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
    | "test_completed"
    | "pending"
    | "to_be_sent"
    | "sent"
    | "error";

export interface SmartBillInvoice {
    id: number;
    status: string;
    payment_status: string;
    invoice_series: string | null;
    invoice_number: string | null;
    file_url: string | null;
    download_url: string | null;
    document_url: string | null;
    document_view_url: string | null;
    error_message: string | null;
    issued_at: string | null;
    payment_registered_at: string | null;
}

export interface ReportPurchase {
    id: number;
    status: string;
    amount_total: number | null;
    currency: string;
    paid_currency: string | null;
    customer_email: string | null;
    customer_name: string | null;
    stripe_checkout_session_id: string | null;
    stripe_payment_intent_id: string | null;
    paid_at: string | null;
    smart_bill_invoice?: SmartBillInvoice | null;
}

export interface Report {
    id: number;
    report_type: ReportType;
    url: string;
    email: string | null;
    is_test: boolean;
    status: ReportStatus;
    report_url: string | null;
    page_token: string | null;
    error_message: string | null;
    processed_at: string | null;
    created_at: string;
    updated_at: string;
    latest_purchase?: ReportPurchase | null;
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
    report_ready_notification_emails: string;
    pricing_rental_living_eur: string;
    pricing_buying_living_eur: string;
    pricing_exchange_rate_eur_ron: string;
    stripe_product_rental_living: string;
    stripe_product_buying_living: string;
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
    dataLayerEvents?: DataLayerEvent[];
}

export interface AppFlags {
    publicWizardMaintenance: boolean;
    publicLocaleSwitcher: boolean;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
    appFlags: AppFlags;
    flash: FlashMessages;
    locale: string;
    supportedLocales: string[];
    publicLocales: string[];
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
