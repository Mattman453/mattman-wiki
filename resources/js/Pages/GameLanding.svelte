<script>
    import { onMount } from "svelte";
    import Layout from "../Components/Layout.svelte";
    import { inertia } from "@inertiajs/svelte";

    let { gameInfo } = $props();

    let isDropdownOpen = $state([]);

    onMount(() => {
        if (gameInfo.headings) {
            for (let i = 0; i < gameInfo.headings.length; i++) {
                isDropdownOpen[gameInfo.headings[i].title] = false;
            }
        }
    });

    function handleDropdownClick(event) {
        isDropdownOpen[event.target.id] = !isDropdownOpen[event.target.id];
    }
</script>

<Layout {gameInfo}>
    <div class="dropdown flex justify-content-center align-items-center">
        {#each gameInfo.headings as heading (heading.title)}
            <!-- svelte-ignore a11y_no_static_element_interactions -->
            <div class="flex column justify-content-center align-items-center" style="max-width: 100px;" onmouseleave={() => isDropdownOpen[heading.title] = false}>
                <a use:inertia class="heading-link" href="/{gameInfo.link + '/' + heading.title}" onmouseenter={() => isDropdownOpen[heading.title] = true}>{heading.title}</a>
                {#if isDropdownOpen[heading.title]}
                    {#each heading.subheadings as subheading (subheading.title)}
                        <a use:inertia href="/{gameInfo.link + '/' + heading.title + '/' + subheading.title}">{subheading.title}</a>
                    {/each}
                {/if}
            </div>
        {/each}
    </div>
    <hr style="margin: 0;">
    {#each gameInfo.sections as section (section.title)}
        {#if section.title != gameInfo.sections[0].title}
            <hr>
        {/if}
        <div class="title-1">
            {section.title}
        </div>
        {#each section.body as body}
            <div class="title-6" style="margin: 2em;">
                {body}
            </div>
        {/each}
    {/each}
</Layout>

<style lang="scss">
    .dropdown {
        overflow-x: auto;
    }

    .title-1 {
        margin: 1em;
    }

    .title-6 {
        margin: 2em;
    }

    a {
        padding: 0.3em;
        border: 1px solid black;
        border-top: 0;
        background-color: rgba(0, 0, 0, 0.05);
        width: 100%;
        text-align: center;
        text-decoration: none;
        color: black;
    }
    
    .heading-link {
        border-top: 1px solid black;
        border-radius: 15px 15px 0 0;
    }
</style>
