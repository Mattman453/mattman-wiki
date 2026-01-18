<script>
    import { inertia } from "@inertiajs/svelte";
    import { slide } from "svelte/transition";

    let { title, children, link, visible = $bindable(), ...otherProps } = $props();
</script>

<div class="flex justify-content-space-between align-items-center holder" style="padding: 0.5em 2em;">
    {#if link}
        <a use:inertia href={link}>{title}</a>
    {:else}
        <div>{title}</div>
    {/if}
    <!-- svelte-ignore a11y_consider_explicit_label -->
    <button class="flex" style="justify-content: right; width: 100%;" onclick={() => visible = !visible}>
        <i class="fa-solid fa-chevron-down" class:rotate={!visible}></i>
    </button>
</div>

{#if visible}
    <div transition:slide={{duration: 300}}>
        {@render children()}
    </div>
{/if}

<style lang="scss">
    .fa-chevron-down {
        transition: transform 0.3s ease;

        &.rotate {
            transform: rotate(-90deg);
        }
    }

    .holder {
        flex: 1 6;
    }

    button {
        border: none;
        background-color: white;
        cursor: pointer;
    }

    a {
        text-decoration: none;
        color: black;
        font-size: 18px;
        font-weight: bold;
    }
</style>
