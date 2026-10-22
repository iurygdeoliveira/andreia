import { defineConfig, lazyPlugins } from "vite-plus";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    staged: {
        "*": "vp check --fix",
    },
    fmt: {
        ignorePatterns: [
            "dist/**",
            "public/**",
            "vendor/**",
            "landing page/**",
            "docs/**",
            "sdd/**",
            "cursos/**",
        ],
    },
    lint: {
        ignorePatterns: [
            "dist/**",
            "public/**",
            "vendor/**",
            "landing page/**",
            "docs/**",
            "sdd/**",
            "cursos/**",
        ],
        jsPlugins: [{ name: "vite-plus", specifier: "vite-plus/oxlint-plugin" }],
        rules: { "vite-plus/prefer-vite-plus-imports": "error" },
        options: { typeAware: true, typeCheck: true },
    },
    test: {
        include: ["resources/**/*.{test,spec}.{js,mjs,cjs,ts,mts,cts,jsx,tsx}"],
        exclude: ["vendor/**", "node_modules/**", "dist/**"],
        passWithNoTests: true,
    },
    plugins: lazyPlugins(() => [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/website.css",
                "resources/js/app.js",
                "resources/css/filament/admin/theme.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ]),
});
