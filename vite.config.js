import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.js",
                "resources/js/sb-admin-2.js",
                "resources/css/app.css",
            ],
            refresh: true,
        }),
    ],
});
