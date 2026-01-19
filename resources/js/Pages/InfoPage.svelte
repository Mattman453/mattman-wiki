<script>
    import Layout from "../Components/Layout.svelte";

    let { gameInfo, page, ...otherProps } = $props();
</script>

<Layout {gameInfo} {...otherProps}>
    <div class="flex column align-items-center">
        <div class="title-1" style="margin-top: 0;">
            {page.page ?? page.subtitle ?? page.game}
        </div>
        <hr>
        <div class="flex column" style="max-width: 1200px;">
            {#each page.sections as section (section.title)}
                {#if section.title != page.sections[0].title}
                    <hr>
                {/if}
                <div class="flex column">
                    <div class="title-1">
                        {section.title}
                    </div>
                    {#if section.body.type == "text"}
                        <div class="title-6">
                            {section.body.data}
                        </div>
                    {/if}
                </div>
            {/each}
        </div>
    </div>
</Layout>

<style lang="scss">
    .title-1 {
        margin: 1em;
    }

    .title-6 {
        margin: 1em 2em;
        font-size: 16px;
        white-space: pre-line;
    }

    hr {
        width: 95%;
    }
</style>
