<script>
    import { inertia } from "@inertiajs/svelte";
    import Layout from "../Components/Layout.svelte";

    let { games } = $props();

    function generateRandomHex(length) {
        let hex = "";
        const hexChars = "0123456789abcdef";
        for (let i = 0; i < length; i++) {
            hex += hexChars[Math.floor(Math.random() * 16)];
        }
        return hex;
    }
</script>

<Layout>
    <h1 style="text-align: center;">Welcome to the Home of Matt</h1>
    <div class="flex game-container">
        {#each games as game (game.title)}
            <a use:inertia href="/game/{game.link ?? ""}" class="game flex column">
                <div class="game-image">
                    <!-- Final Image -->
                    <img src="{game.image ?? "https://placehold.co/128x128/"+generateRandomHex(6)+"/cccccc.png?font=lato"}" alt="{game.title ?? "game"} logo">
                </div>
                <div class="game-title">{game.title}</div>
            </a>
        {/each}
    </div>
</Layout>

<style lang="scss">
    .game-container {
        flex-wrap: wrap;
        margin: 2em;

        .game {
            min-width: 100px;
            max-width: 400px;
            min-height: 100px;
            max-height: 400px;
            justify-content: center;
            align-items: center;
            text-align: center;
            border: 1px solid black;
            border-radius: 15px;
            margin: 1em;
            box-shadow: 0 1px 30px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            color: black;

            .game-image {
                max-height: 256px;
                max-width: 256px;
                min-height: 64px;
                min-width: 64px;
                margin: 1em 1em 0.1em 1em;

                img {
                    max-height: 256px;
                    max-width: 256px;
                    min-height: 64px;
                    min-width: 64px;
                }
            }

            .game-title {
                margin: 0.1em 1em 1em 1em;
            }
        }
    }
</style>
