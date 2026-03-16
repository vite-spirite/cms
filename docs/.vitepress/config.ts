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
                    ],
                },
            ],
            '/modules': [
                {
                    text: 'Modules',
                    items: [
                        { text: 'Modules', link: '/modules/' },
                        { text: 'Creating module', link: '/modules/creating-module' },
                    ],
                },
            ],
            '/api': [
                {
                    text: 'API',
                    items: [
                        { text: 'API', link: '/api/' },
                        { text: 'BaseModuleProvider', link: '/api/base-module-provider' },
                    ],
                },
            ],
        },

        socialLinks: [{ icon: 'github', link: 'https://github.com/vite-spirite/cms' }],
    },
});
