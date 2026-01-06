<script>
    import { inertia } from "@inertiajs/svelte";

    let { children, ...otherProps } = $props();
</script>

<svelte:head>
    <link rel="icon" href="/userIcon.ico">
    <title>Matt's Home</title>
    <script src="https://kit.fontawesome.com/0cdd07cc84.js" crossorigin="anonymous"></script>
</svelte:head>

<div class="header flex justify-content-center align-items-center">
    {#if otherProps.gameInfo?.image}
        <img class="game-logo" src="{otherProps.gameInfo.image}" alt="{otherProps.gameInfo.title ?? "game"} logo">
    {/if}
    <a use:inertia href="/" style="order: 2;">
        <h1>
            Matt's Game Guides
        </h1>
    </a>
</div>

<div style="margin: 2em;">
    {@render children()}
</div>

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
        z-index: 9999;
        background-color: white;
        gap: 20px;

        @media screen and (max-width: 800px){
            height: 50px;
        }
        
        a {
            padding: 5px 10px;
        }
        
        .game-logo {
            max-width: 100px;
            max-height: 75px;

            @media screen and (max-width: 800px){
                height: 50px;
            }
        }
    }

    .footer {
        margin: 1em;
    }
</style>
