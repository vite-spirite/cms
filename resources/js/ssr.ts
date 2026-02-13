import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer(
    (page) =>
        createInertiaApp({
            page,
            render: renderToString,
            title: (title) => (title ? `${title} - ${appName}` : appName),
            resolve: resolvePage,
            setup: ({ App, props, plugin }) => createSSRApp({ render: () => h(App, props) }).use(plugin),
        }),
    { cluster: true },
);

function resolvePage(name: string) {
    if (name.includes('::')) {
        const [namespace, path] = name.split('::');

        const imports = import.meta.glob<DefineComponent>([
            '../../app/Core/*/Resources/js/Pages/**/*.vue',
            '../../app/Modules/*/Resources/js/Pages/**/*.vue',
        ]);

        const coreComponentPath = `../../app/Core/${namespace}/Resources/js/Pages/${path}.vue`;

        if (Object.keys(imports).includes(coreComponentPath)) {
            return resolvePageComponent(coreComponentPath, imports);
        } else {
            const moduleComponentPath = `../../app/Modules/${namespace}/Resources/js/Pages/${path}.vue`;

            if (Object.keys(imports).includes(moduleComponentPath)) {
                return resolvePageComponent(moduleComponentPath, imports);
            }
        }
    }

    return resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue'));
}
