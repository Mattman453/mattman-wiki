<script>
    import { onMount } from "svelte";
    import Layout from "../Components/Layout.svelte";

    let { gameInfo, page, ...otherProps } = $props();

    let isDropdownOpen = $state([]);

    onMount(() => {
        if (gameInfo.headings) {
            for (let i = 0; i < gameInfo.headings.length; i++) {
                isDropdownOpen[gameInfo.headings[i].title] = false;
            }
        }
    });
</script>

<Layout {gameInfo} {...otherProps}>
    {#each page.sections as section (section.title)}
        {#if section.title != page.sections[0].title}
            <hr>
        {/if}
        <div class="flex column align-items-center">
            <div style="max-width: 1200px;" class="flex column">
                <div class="title-1">
                    {section.title}
                </div>
                {#each section.body as body}
                    <div class="title-6" style="margin: 1em 2em; font-size: 16px;">
                        {body}
                    </div>
                {/each}
            </div>
        </div>
    {/each}
</Layout>

<style lang="scss">
    .title-1 {
        margin: 1em;
    }

    .title-6 {
        margin: 2em;
    }
</style>
