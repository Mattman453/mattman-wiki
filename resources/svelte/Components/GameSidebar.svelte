<script>
    import { inertia, router } from "@inertiajs/svelte";
    import Dropdown from "./Dropdown.svelte";
    import { convertSpaceToUnderscore } from "../../js/helper";
    import { onDestroy, onMount } from "svelte";

    let { 
        csrfToken,
        gameInfo,
        openNavigator = $bindable(false), 
        visible = $bindable([]), 
        user,
    } = $props();

    let addingSection = $state(false);
    let addingPage = $state(false);
    let success = $state('');
    let error = $state('');
    
    let focusTimeout, successTimeout, errorTimeout;

    onMount(() => {
        for (let i = 0; i < gameInfo.sections.length; i++) visible[i] = false;
    });

    onDestroy(() => {
        clearTimeout(focusTimeout);
    })

    function newPageHandler(e) {
        e.preventDefault();
        error = '';
        success = '';
        if (e.target.id.includes("section")) {
            if (!addingSection) {
                addingSection = true;
                focusTimeout = setTimeout(() => {
                    document.getElementById("section_name").focus();
                    focusTimeout = null;
                }, 1);
                return;
            }
        }
        else {
            if (!addingPage) {
                addingPage = true;
                focusTimeout = setTimeout(() => {
                    document.getElementById("page_name").focus();
                    focusTimeout = null;
                }, 1);
                return;
            }
        }

        let formData = new FormData(e.target);
        formData.append('game', gameInfo.game);
        fetch('/game/new_page', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        })
        .then(response => {
            response.json().then(data => {
                switch (response.status) {
                    case 200:
                        addingSection = false;
                        success = data.message + " Reloading...";
                        successTimeout = setTimeout(() => {
                            success = '';
                            router.reload();
                            openNavigator = false;
                            successTimeout = null;
                        }, 500);
                        break;
                    case 400:
                        error = data.error;
                        errorTimeout = setTimeout(() => {
                            error = '';
                            errorTimeout = null;
                        }, 5000);
                        break;
                    default:
                        console.error(`Unexpected response status ${response.status} with messages:`);
                        console.error(data);
                        break;
                }
            })
            .catch(error => {
                console.error(error);
                error = "Page has expired. Please reload the page and try again.";
            });
        })
        .catch(error => {
            console.error(error);
            error = "Page has expired. Please reload the page and try again.";
        });
    }
</script>

<div class="sidebar-container">
    {#each gameInfo.sections as section, index (section.subtitle)}
        <Dropdown title={section.subtitle} link="/game/{convertSpaceToUnderscore(gameInfo.game)}/{convertSpaceToUnderscore(section.subtitle)}" bind:openNavigator bind:visible={visible[index]}>
            <div class="flex column" style="margin-left: 2em;">
                {#each section.sections as page}
                    <a class="page-link" onclick={() => openNavigator = false} use:inertia href="/game/{convertSpaceToUnderscore(gameInfo.game)}/{convertSpaceToUnderscore(section.subtitle)}/{convertSpaceToUnderscore(page)}">{page}</a>
                {/each}
            </div>
            {#if user?.roles?.includes("admin") || user?.roles?.includes("author")}
                {#if addingPage}
                    <form id="new_page_form" class="flex new-page-form align-items-center" onsubmit={newPageHandler}>
                        <input type="hidden" id="subtitle" name="subtitle" value={section.subtitle}>
                        <input type="text" id="page_name" name="page_name" placeholder="Page Name" class="page-name" minlength="1" required>
                        <button type="submit" class="page-button">Submit</button>
                    </form>
                {:else}
                    <button id="new_page_button_{index}" class="new-page-button" style="margin: 5px 2.5em; {addingPage ? 'cursor: text;' : 'cursor: pointer;'}" onclick={newPageHandler}>
                        [+] New Page
                    </button>
                {/if}
            {/if}
        </Dropdown>
        <hr>
    {/each}
</div>

{#if user?.roles?.includes("admin") || user?.roles?.includes("author")}
    {#if addingSection}
        <form id="new_section_form" class="flex new-section-form" onsubmit={newPageHandler}>
            <input type="text" id="section_name" name="section_name" placeholder="Section Name" class="section-name" minlength="1" required>
            <button type="submit" class="section-button">Submit</button>
        </form>
    {:else}
        <button id="new_section_button" class="new-section-button" style="margin: 5px 2em; {addingSection ? 'cursor: text;' : 'cursor: pointer;'}" onclick={newPageHandler}>
            [+] New Section
        </button>
    {/if}
{/if}

{#if success}
    <div class="title-3" style="color: green; margin: 5px 2em;">{success}</div>
{/if}

{#if error}
    <div class="title-3" style="color: red; margin: 5px 2em;">{error}</div>
{/if}

<style lang="scss">
    .sidebar-container {
        max-width: 100vw;
    }

    .page-link {
        text-decoration: none;
        color: black;
        padding: 5px 10px;
    }

    .new-section-button {
        border: none;
        background-color: white;
        font-size: 16px;
        font-weight: bold;
    }

    .new-page-button {
        border: none;
        background-color: white;
        font-size: 16px;
        font-weight: bold;
    }

    .new-section-form {
        margin: 5px 2em;
    }
    
    .new-page-form {
        margin: 5px 2em;
    }

    .section-name {
        font-size: 16px;
        padding: 0.2em;
        border-radius: 10px 0 0 10px;
        border: 1px solid black;
    }
    
    .page-name {
        font-size: 16px;
        padding: 0.2em;
        margin: 5px 0 5px 0.5em;
        border-radius: 10px 0 0 10px;
        border: 1px solid black;
    }

    .section-button {
        border-radius: 0 10px 10px 0;
        border: 1px solid black;
    }
    
    .page-button {
        border-radius: 0 10px 10px 0;
        border: 1px solid black;
        height: 1.95em;
    }
</style>
