import "../css/app.css";
import "./bootstrap";

import { Head, createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ComponentType, ReactElement, ReactNode, createElement } from "react";
import { createRoot } from "react-dom/client";

import PublicAnalyticsBridge from "@/Components/PublicAnalyticsBridge";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

type LayoutComponent = ComponentType<
    { children: ReactNode } & Record<string, unknown>
>;
type LayoutFunction = (page: ReactElement) => ReactNode;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.tsx`,
            import.meta.glob("./Pages/**/*.tsx"),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        const pageProps = props.initialPage.props as {
            seoIndexing?: boolean;
            seo?: {
                canonical?: string | null;
                alternates?: Record<string, string | null>;
                xDefault?: string | null;
            };
        };
        const seo = pageProps.seo ?? {};

        root.render(
            <App {...props}>
                {({ Component, props: componentProps, key }) => {
                    const page = <Component key={key} {...componentProps} />;
                    const layout = Component.layout as
                        | LayoutComponent[]
                        | LayoutFunction
                        | undefined;

                    const renderedPage = Array.isArray(layout)
                        ? [...layout]
                              .reverse()
                              .reduce<ReactNode>((children, Layout) => {
                                  return createElement(Layout, {
                                      children,
                                      ...(componentProps as Record<
                                          string,
                                          unknown
                                      >),
                                  });
                              }, page)
                        : typeof layout === "function"
                          ? layout(page)
                          : page;

                    return (
                        <>
                            <Head>
                                {pageProps.seoIndexing === false ? (
                                    <meta
                                        head-key="robots"
                                        name="robots"
                                        content="noindex, nofollow, noarchive"
                                    />
                                ) : null}
                                {seo.canonical ? (
                                    <link
                                        head-key="canonical"
                                        rel="canonical"
                                        href={seo.canonical}
                                    />
                                ) : null}
                                {Object.entries(seo.alternates ?? {}).map(
                                    ([hrefLang, href]) =>
                                        href ? (
                                            <link
                                                key={hrefLang}
                                                head-key={`alternate-${hrefLang}`}
                                                rel="alternate"
                                                hrefLang={hrefLang}
                                                href={href}
                                            />
                                        ) : null,
                                )}
                                {seo.xDefault ? (
                                    <link
                                        head-key="alternate-x-default"
                                        rel="alternate"
                                        hrefLang="x-default"
                                        href={seo.xDefault}
                                    />
                                ) : null}
                            </Head>
                            <PublicAnalyticsBridge />
                            {renderedPage}
                        </>
                    );
                }}
            </App>,
        );
    },
    progress: {
        color: "#4B5563",
    },
});
