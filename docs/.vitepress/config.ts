import { defineConfig } from 'vitepress';

// https://vitepress.dev/reference/site-config
export default defineConfig({
    title: 'CMS',
    description: 'CMS documentation',
    themeConfig: {
        // https://vitepress.dev/reference/default-theme-config
        nav: [
            { text: 'Home', link: '/' },
            { text: 'Guide', link: '/guide/' },
            { text: 'Modules', link: '/modules/' },
            { text: 'API', link: '/api/' },
        ],

        sidebar: {
            '/guide': [
                {
                    text: 'Introduction',
                    items: [
                        { text: 'Introduction', link: '/guide' },
                        { text: 'Installation', link: '/guide/installation' },
                        { text: 'Quick start', link: '/guide/quick-start' },
                    ],
                },
            ],
            '/modules': [
                {
                    text: 'Getting Started',
                    items: [
                        { text: 'Overview', link: '/modules/' },
                        { text: 'Creating a module', link: '/modules/creating-module' },
                        { text: 'Module communication', link: '/modules/module-communication' },
                        { text: 'Extension points', link: '/modules/extension-points' },
                        { text: 'Frontend conventions', link: '/modules/frontend' },
                        { text: 'Creating a block', link: '/modules/creating-block' },
                        { text: 'Permissions reference', link: '/modules/permissions-reference' },
                    ],
                },
                {
                    text: 'Core Modules',
                    items: [
                        { text: 'Auth', link: '/modules/core/auth' },
                        { text: 'Permissions', link: '/modules/core/permissions' },
                        { text: 'Navigation', link: '/modules/core/navigation' },
                    ],
                },
            ],
            '/api': [
                {
                    text: 'API',
                    items: [
                        { text: 'Overview', link: '/api/' },
                        { text: 'BaseModuleProvider', link: '/api/base-module-provider' },
                        { text: 'ModuleHelper', link: '/api/module-helper' },
                        { text: 'NavigationManager', link: '/api/navigation-manager' },
                        { text: 'PermissionRegistry', link: '/api/permission-registry' },
                        { text: 'Events', link: '/api/events' },
                    ],
                },
            ],
        },

        socialLinks: [{ icon: 'github', link: 'https://github.com/vite-spirite/cms' }],
    },
});
