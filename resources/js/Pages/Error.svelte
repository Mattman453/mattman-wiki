<script>
    import { inertia } from "@inertiajs/svelte";
    import Layout from "../Components/Layout.svelte";

    let { missingGame } = $props();
</script>

<Layout>
    <div class="flex column justify-content-center align-items-center">
        <h1>Unable to Find Page for {missingGame ?? "Unknown Game"}.</h1>
        <a use:inertia href="/">
            <h3>
                Click Here to Return to The Home Page.
            </h3>
        </a>
    </div>
</Layout>

<style lang="scss">

</style>
