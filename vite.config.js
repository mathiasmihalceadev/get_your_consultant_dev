import { defineConfig, loadEnv } from "vite";
import path from "node:path";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), "");
    const allowedOrigins = [
        env.APP_URL,
        env.APP_DOMAIN_EN,
        env.APP_DOMAIN_RO,
        "http://localhost:8000",
        "http://127.0.0.1:8000",
    ].filter(
        (value, index, values) => value && values.indexOf(value) === index,
    );

    return {
        resolve: {
            alias: {
                "object-inspect/util.inspect.js": path.resolve(
                    process.cwd(),
                    "resources/js/shims/object-inspect-util.ts",
                ),
            },
        },
        server: {
            host: "0.0.0.0",
            port: 5173,
            strictPort: true,
            origin: "http://localhost:5173",
            headers: {
                "Access-Control-Allow-Origin": "*",
            },
            cors: {
                origin: allowedOrigins,
            },
            hmr: {
                host: "localhost",
                port: 5173,
                clientPort: 5173,
            },
        },
        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.tsx"],
                refresh: true,
            }),
            react(),
            tailwindcss(),
        ],
    };
});
