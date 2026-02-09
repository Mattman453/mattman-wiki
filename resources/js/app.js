import { createInertiaApp } from '@inertiajs/svelte';
import './bootstrap';
import '../scss/app.scss';
import { mount } from 'svelte';
import Layout from './Components/Layout.svelte';

createInertiaApp({
    resolve: async name => {
        const pages = import.meta.glob('./Pages/**/*.svelte', { eager: false });
        let page = await pages[`./Pages/${name}.svelte`]();
        return { default: page.default, layout: page.layout || Layout };
    },
    setup({ el, App, props }) {
        mount(App, { target: el, props });
    },
});
