import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { resolve } from 'path';
import { readdirSync, statSync } from 'node:fs';
import ui from '@nuxt/ui/vite';

function getModulesAliases() {
    const coreModulePath = resolve(__dirname, 'app/Core');
    const modulesPath = resolve(__dirname, 'app/Modules');

    const aliases: Record<string, string> = {};

    try {
        const coreModule = readdirSync(coreModulePath);

        coreModule.forEach((module) => {
            const modulePath = resolve(coreModulePath, module);
            const resourcePath = resolve(modulePath, 'Resources/js');

            if (statSync(coreModulePath).isDirectory()) {
                aliases[`@modules/${module}`] = resourcePath;
            }
        });

        const modules = readdirSync(modulesPath);
        modules.forEach((module) => {
            const modulePath = resolve(modulesPath, module);
            const resourcePath = resolve(modulePath, 'Resources/js');

            if (statSync(coreModulePath).isDirectory() && aliases[`@modules/${module}`] === undefined) {
                aliases[`@modules/${module}`] = resourcePath;
            } else {
                console.warn(`Custom module ${module} not loaded`);
            }
        });
    } catch (error) {
        console.warn('Module directory not found');
    }
    console.log(aliases);
    return aliases;
}

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        wayfinder({
            formVariants: true,
            patterns: ['app/Core/**/Routes/*.php', 'app/Modules/**/Routes/*.php'],
        }),
        ui({
            router: 'inertia',
        }),
    ],
    resolve: {
        alias: {
            'ziggy-js': resolve('vendor/tightenco/ziggy'),
            '@': resolve(__dirname, 'resources/js'),
            ...getModulesAliases(),
        },
    },
    server: {
        fs: {
            allow: [
                resolve(__dirname, 'resources'),
                resolve(__dirname, 'app/Core'),
                resolve(__dirname, 'app/Modules'),
                resolve(__dirname, 'node_modules'),
                resolve(__dirname, 'vendor/tightenco'),
            ],
        },
    },
});
