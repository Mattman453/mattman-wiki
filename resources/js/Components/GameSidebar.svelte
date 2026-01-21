<script>
    import { inertia } from "@inertiajs/svelte";
    import Dropdown from "../Components/Dropdown.svelte";
    import { convertSpaceToUnderscore } from "../helper";

    let { openNavigator = $bindable(false), ...otherProps } = $props();
</script>
{#each otherProps.gameInfo.sections as section (section.subtitle)}
    <Dropdown title={section.subtitle} link="/game/{convertSpaceToUnderscore(otherProps.gameInfo.game)}/{convertSpaceToUnderscore(section.subtitle)}">
        <div class="flex column" style="margin-left: 2em;">
            {#each section.sections as subSection}
                <a onclick={() => openNavigator = false} use:inertia href="/game/{convertSpaceToUnderscore(otherProps.gameInfo.game)}/{convertSpaceToUnderscore(section.subtitle)}/{convertSpaceToUnderscore(subSection)}">{subSection}</a>
            {/each}
        </div>
    </Dropdown>
{/each}

<style lang="scss">
    a {
        text-decoration: none;
        color: black;
        padding: 5px 10px;
    }
</style>
