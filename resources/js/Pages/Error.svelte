<script>
    import { inertia } from "@inertiajs/svelte";

    let { missingPage } = $props();
</script>

<div class="flex column justify-content-center align-items-center">
    <h1>Unable to Find Page: {missingPage ?? "Unknown Page"}.</h1>
    <a use:inertia href="/">
        <h3>
            Click Here to Return to The Home Page.
        </h3>
    </a>
</div>
