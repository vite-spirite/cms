import { createInertiaApp } from '@inertiajs/vue3';
import ui from '@nuxt/ui/vue-plugin';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';

import { ZiggyVue } from 'ziggy-js';
import BlockRegistry from '../../app/Modules/PageBuilder/Resources/js/blockRegistry';
import FieldRegistry from '../../app/Modules/PageBuilder/Resources/js/fieldRegistry';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const pinia = createPinia();

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
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
    },
    setup({ el, App, props, plugin }) {
        BlockRegistry.all();
        FieldRegistry.all();

        const activeModules = (props.initialPage.props.activeModules as string[]) ?? [];
        const allExtensions = import.meta.glob(['../../app/Core/*/Resources/js/extensions.ts', '../../app/Modules/*/Resources/js/extensions.ts']);

        const activeExtensions = Object.entries(allExtensions).filter(([path]) => activeModules.some((module) => path.includes(`/${module}/`)));

        import.meta.glob(['../../app/Core/*/Resources/js/blocks.ts', '../../app/Modules/*/Resources/js/blocks.ts'], { eager: true });
        import.meta.glob(['../../app/Core/*/Resources/js/fields.ts', '../../app/Modules/*/Resources/js/fields.ts'], { eager: true });

        Promise.all(Object.values(activeExtensions).map(([, ext]) => ext())).then(() => {
            createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ui)
                .use(ZiggyVue)
                .use(pinia)
                .mount(el);
        });
    },
    progress: {
        color: '#4B5563',
    },
});
