<script>
    import { inertia } from "@inertiajs/svelte";

    let { children } = $props();
</script>

<svelte:head>
    <title>Matt's Home</title>
    <script src="https://kit.fontawesome.com/0cdd07cc84.js" crossorigin="anonymous"></script>
</svelte:head>

<div class="header flex justify-content-center align-items-center">
    <a use:inertia href="/">
        <h1>
            Matt's Game Guides
        </h1>
    </a>
</div>

{@render children()}

<hr>
<div class="footer flex justify-content-center">
    <a use:inertia href="/about">
        <div class="title-2">
            About Me
        </div>
    </a>
</div>

<style lang="scss">
    .header {
        position: sticky;
        top: 0;
        box-shadow: 0 4px 60px rgba(0, 0, 0, 0.2);
        height: 75px;

        a {
            padding: 5px 10px;
        }
    }
</style>
