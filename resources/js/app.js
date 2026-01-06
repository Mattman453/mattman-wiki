import { createInertiaApp } from '@inertiajs/svelte';
import './bootstrap';
import '../css/app.scss';
import { mount } from 'svelte';
// import Layout from './Components/Layout.svelte';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.svelte', { eager: false });
        let page = pages[`./Pages/${name}.svelte`]();
        return page;
        // Default layout. Useful if pages are bundled as one. Set eager to true
        // return { default: page.default, layout: page.layout || Layout };
    },
    setup({ el, App, props }) {
        mount(App, { target: el, props });
    },
});
