<script>
    import { inertia, router } from "@inertiajs/svelte";
    import { isMobile } from "../stores";
    import { scale, slide } from "svelte/transition";
    import Dropdown from "./Dropdown.svelte";
    import { convertSpaceToUnderscore } from "../helper";

    let { children, ...otherProps } = $props();

    let error = $state('');
    let openNavigator = $state(false);
    let transition = $state(false);


    function logoutHandler(e) {
        e.preventDefault();
        error = '';
        let formData = new FormData(e.target);
        fetch('/logout', {
            method: 'POST',
            headers: {
                "X-CSRF-TOKEN": otherProps.csrfToken,
            },
            body: formData,
        })
        .then(response => {
            response.json().then(data => {
                switch(response.status) {
                    case 302:
                        router.get(data.redirect);
                        break;
                    default:
                        console.error(`Unexpected response status ${response.status} with messages:`);
                        console.error(data);
                        break;
                }
            })
            .catch(exception => {
                console.error(exception);
                error = "Page has expired. Please reload the page and try again."
            });
        })
        .catch(exception => {
            console.error(exception);
        });
    }
</script>

<svelte:head>
    <title>Matt's Home</title>
    <script src="https://kit.fontawesome.com/0cdd07cc84.js" crossorigin="anonymous"></script>
</svelte:head>

<div class="header flex align-items-center justify-content-space-between">
    {#if otherProps.gameInfo}
        <!-- svelte-ignore a11y_consider_explicit_label -->
        <button onclick={() => {if (openNavigator) {transition = true; openNavigator = false;} else {transition = true; openNavigator = true;}}} style="all: unset; cursor: pointer; z-index: 9999;">
            {#if !openNavigator && transition == false}
                <i class="fa-solid fa-bars" transition:scale={{duration: 150, opacity: 1}} onoutroend={() => transition = false}></i>
            {:else if openNavigator && transition == false}
                <i class="fa-solid fa-xmark" transition:scale={{duration: 150, opacity: 1}} onoutroend={() => transition = false}></i>
            {/if}
        </button>
    {:else}
        <div></div>
    {/if}
    <div class="flex justify-content-center align-items-center">
        {#if otherProps.gameInfo?.image}
            <a use:inertia href="/game/{convertSpaceToUnderscore(otherProps.gameInfo.game)}">
                <img class="game-logo" src="{otherProps.gameInfo.image}" alt="{otherProps.gameInfo.title ?? "game"} logo">
            </a>
        {/if}
        <a use:inertia href="/">
            <div class="{$isMobile ? 'title-3' : 'title-1'}">
                Matt's Game Guides
            </div>
        </a>
    </div>
    {#if otherProps.user}
        <form id="logout" onsubmit={logoutHandler}>
            <button type="submit" style="all: unset; cursor: pointer; font-weight: bold;">
                Logout
            </button>
        </form>
    {:else}
        <div style="">
            <a use:inertia href="/login">
                <div class="title-3">
                    Login
                </div>
            </a>
        </div>
    {/if}
    {#if openNavigator}
        <div class="menu" transition:slide={{axis: 'x'}}>
            <div class="flex column" style="margin-top: 3em;">
                {#each otherProps.gameInfo.sections as section (section.subtitle)}
                    <Dropdown title={section.subtitle} link="/game/{convertSpaceToUnderscore(otherProps.gameInfo.game)}/{convertSpaceToUnderscore(section.subtitle)}">
                        <div class="flex column" style="margin-left: 2em;">
                            {#each section.sections as subSection}
                                <a use:inertia href="/game/{convertSpaceToUnderscore(otherProps.gameInfo.game)}/{convertSpaceToUnderscore(section.subtitle)}/{convertSpaceToUnderscore(subSection)}">{subSection}</a>
                            {/each}
                        </div>
                    </Dropdown>
                {/each}
            </div>
        </div>
        <!-- svelte-ignore a11y_click_events_have_key_events -->
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div class="background" transition:slide={{axis: 'x'}} onclick={() => openNavigator = false}></div>
    {/if}
</div>


<div style="margin: 1em 2em;">
    {@render children()}
</div>

<hr>
<div class="footer flex justify-content-center">
    <a use:inertia href="/about">
        <div class="title-3">
            About
        </div>
    </a>
    <div style="color: red;">
        {error}
    </div>
</div>

<style lang="scss">
    @use "../../css/variables";

    a {
        text-decoration: none;
        color: black;
    }

    .header {
        position: sticky;
        top: 0;
        box-shadow: 0 4px 60px rgba(0, 0, 0, 0.2);
        height: 3em;
        z-index: 999;
        background-color: white;
        gap: 20px;
        padding: 0 2em;

        @media screen and (max-width: variables.$mobileVW) {
            height: 2.5em;
        }

        .menu {
            background-color: white;
            z-index: 9998;
            position: fixed;
            top: 0;
            left: 0;
            width: 500px;
            height: 100vh;
            gap: 1em;
            box-sizing: border-box;
        }

        .background {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9997;
        }
        
        a {
            padding: 5px 10px;
        }
        
        .game-logo {
            max-width: 100px;
            max-height: 75px;

            @media screen and (max-width: variables.$mobileVW) {
                height: 50px;
            }
        }
    }

    .footer {
        margin: 1em;
    }
</style>
