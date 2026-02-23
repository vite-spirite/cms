import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import ExtensionRegistry from '@modules/Module/ExtensionRegistry';
import ui from '@nuxt/ui/vue-plugin';

import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createSSRApp, h } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const allExtensions = import.meta.glob(['../../app/Core/*/Resources/js/extensions.ts', '../../app/Modules/*/Resources/js/extensions.ts']);

createServer(
    (page) => {
        const activeModules = (page.props.activeModules as string[]) ?? [];

        ExtensionRegistry.reset();

        const activeExtensions = Object.entries(allExtensions).filter(([path]) => activeModules.some((module) => path.includes(`/${module}/`)));

        return Promise.all(activeExtensions.map(([, ext]) => ext())).then(() =>
            createInertiaApp({
                page,
                render: renderToString,
                title: (title) => (title ? `${title} - ${appName}` : appName),
                resolve: resolvePage,
                setup: ({ App, props, plugin }) =>
                    createSSRApp({ render: () => h(App, props) })
                        .use(plugin)
                        .use(ui)
                        .use(ZiggyVue),
            }),
        );
    },
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
