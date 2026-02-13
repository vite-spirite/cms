import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';
import ui from '@nuxt/ui/vue-plugin';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

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
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ui)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
