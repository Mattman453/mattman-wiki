<script>
    import { inertia } from "@inertiajs/svelte";

    let visible = $state(false);
    let { title, children, link, ...otherProps } = $props();
</script>

<div class="flex justify-content-space-between align-items-center holder" style="padding: 0.5em 2em;">
    {#if link}
        <a use:inertia href={link}>{title}</a>
    {:else}
        <div>{title}</div>
    {/if}
    <!-- svelte-ignore a11y_consider_explicit_label -->
    <button class="flex" style="justify-content: right; width: 100%;" onclick={() => visible = !visible}>
        <i class="fa-solid" class:fa-chevron-right={!visible} class:fa-chevron-down={visible}></i>
    </button>
</div>

<div class:hide={!visible}>
    {@render children()}
</div>

<style lang="scss">
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
