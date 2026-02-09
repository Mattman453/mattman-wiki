<script>
    import { inertia } from "@inertiajs/svelte";
    import { convertSpaceToUnderscore } from "../../js/helper";

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

<h1 style="text-align: center;">Welcome to the Home of Matt</h1>
<div class="flex game-container">
    {#each games as game (game.game)}
        <a use:inertia href="/game/{convertSpaceToUnderscore(game.game) ?? ""}" class="game box-shadow flex column">
            <div class="game-image">
                <img src="{game.image ?? "https://placehold.co/128x128/"+generateRandomHex(6)+"/cccccc.png?font=lato"}" alt="{game.title ?? "game"} logo">
            </div>
            <div class="game-title">{game.game}</div>
        </a>
    {/each}
</div>

<style lang="scss">
    a {
        text-decoration: none;
        color: black;
    }

    .game-container {
        flex-wrap: wrap;

        .game {
            min-width: 256px;
            max-width: 400px;
            min-height: 256px;
            max-height: 400px;
            justify-content: center;
            align-items: center;
            text-align: center;
            border: 1px solid black;
            border-radius: 15px;
            margin: 0.5em;
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
