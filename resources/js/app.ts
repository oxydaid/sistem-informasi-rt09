import '../css/app.css';

import { createApp, h, type DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { route } from './lib/route';

// Expose route globally on window
declare global {
    interface Window {
        route: typeof route;
    }
}
window.route = route;

const appName = import.meta.env.VITE_APP_NAME || 'Sistem Informasi RT 09';
const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue', { eager: true });

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const page = pages[`./pages/${name}.vue`];
        if (!page) {
            throw new Error(`Page component not found: ./pages/${name}.vue`);
        }
        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.config.globalProperties.route = route;
        app.use(plugin);
        app.mount(el);
    },
    progress: {
        color: '#009CDE',
        showSpinner: false,
        delay: 50,
    },
});
