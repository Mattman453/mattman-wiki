<script>
    import { inertia } from "@inertiajs/svelte";
    import { slide } from "svelte/transition";

    let {
        children,
        link,
        openNavigator = $bindable(false),
        title,
        visible = $bindable(),
    } = $props();
</script>

<div class="flex justify-content-space-between dropdown-header">
    {#if link}
        <a use:inertia href={link} onclick={() => openNavigator = false}>{title}</a>
    {:else}
        <div>{title}</div>
    {/if}
    <!-- svelte-ignore a11y_consider_explicit_label -->
    <button class="flex" style="justify-content: right; width: 100%; align-items: center;" onclick={() => visible = !visible}>
        <i class="fa-solid fa-chevron-down" class:hide={!visible}></i>
        <i class="fa-solid fa-chevron-right" class:hide={visible}></i>
    </button>
</div>

{#if visible}
    <div transition:slide={{duration: 300}}>
        {@render children()}
    </div>
{/if}

<style lang="scss">
    @use "../../scss/variables";

    .dropdown-header {
        padding: 0.5em 2em;
        align-items: stretch;

        @media screen and (max-width: variables.$mobileVW) {
            padding: 0.5em 1em;
        }
    }

    button {
        border: none;
        background-color: white;
        cursor: pointer;
        justify-content: right;
        margin: 0;
    }

    a {
        text-decoration: none;
        color: black;
        font-size: 18px;
        font-weight: bold;
    }
</style>
