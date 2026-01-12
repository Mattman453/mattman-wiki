<script>
    import { inertia } from "@inertiajs/svelte";
    import { isMobile } from "../stores";

    let { children, ...otherProps } = $props();
</script>

<svelte:head>
    <title>Matt's Home</title>
    <script src="https://kit.fontawesome.com/0cdd07cc84.js" crossorigin="anonymous"></script>
</svelte:head>

<div class="header flex justify-content-center align-items-center">
    {#if otherProps.gameInfo?.image}
        <img class="game-logo" src="{otherProps.gameInfo.image}" alt="{otherProps.gameInfo.title ?? "game"} logo">
    {/if}
    <a use:inertia href="/" style="order: 2;">
        <div class="{$isMobile ? 'title-3' : 'title-1'}">
            Matt's Game Guides
        </div>
    </a>
</div>

<div style="margin: 2em;">
    {@render children()}
</div>

<hr>
<div class="footer flex justify-content-center">
    <a use:inertia href="/about">
        <div class="title-3">
            About
        </div>
    </a>
</div>

<style lang="scss">
    @use "../../css/variables";

    a {
        text-decoration: none;
        color: black;
    }

    .header {
        position: sticky;
        top: 0;
        box-shadow: 0 4px 60px rgba(0, 0, 0, 0.2);
        height: 60px;
        z-index: 9999;
        background-color: white;
        gap: 20px;

        @media screen and (max-width: variables.$mobileVW) {
            height: 50px;
        }
        
        a {
            padding: 5px 10px;
        }
        
        .game-logo {
            max-width: 100px;
            max-height: 75px;

            @media screen and (max-width: variables.$mobileVW) {
                height: 50px;
            }
        }
    }

    .footer {
        margin: 1em;
    }
</style>
