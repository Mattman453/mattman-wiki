import { createInertiaApp } from '@inertiajs/svelte';
import './bootstrap';
import '../scss/app.scss';
import { mount } from 'svelte';
import Layout from '../svelte/Components/Layout.svelte';

createInertiaApp({
    resolve: async name => {
        const pages = import.meta.glob('../svelte/Pages/**/*.svelte', { eager: false });
        let page = await pages[`../svelte/Pages/${name}.svelte`]();
        return { default: page.default, layout: page.layout || Layout };
    },
    setup({ el, App, props }) {
        mount(App, { target: el, props });
    },
});
