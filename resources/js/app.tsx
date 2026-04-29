import "../css/app.css";
import "./bootstrap";

import { Head, createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createElement } from "react";
import { createRoot } from "react-dom/client";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

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
                    const renderedPage = Array.isArray(Component.layout)
                        ? Component.layout
                              .concat(page)
                              .reverse()
                              .reduce((children, Layout) => {
                                  return createElement(Layout, {
                                      children,
                                      ...componentProps,
                                  });
                              })
                        : typeof Component.layout === "function"
                          ? Component.layout(page)
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
