<script>
    import { inertia, router } from "@inertiajs/svelte";
    import { convertSpaceToUnderscore } from "../helper";
    import { isMobile, windowInnerWidth } from "../stores";
    import { onDestroy } from "svelte";
    import { fade, scale, slide } from "svelte/transition";

    let { children, Sidebar, lifetime, ...otherProps } = $props();
    let currentLifetime = $derived(new Date(lifetime + 'Z'));

    let error = $state('');
    let message = $state('');
    let openNavigator = $state(false);
    let visible = $state([]);
    let transition = $state(false);
    let currentTime = $state(new Date().getTime())
    let dateInterval = setInterval(() => currentTime = new Date().getTime(), 100);
    let visibilityTimeout;
    let messageTimeout;

    onDestroy(() => {
        clearTimeout(visibilityTimeout);
        clearInterval(dateInterval);
        clearTimeout(messageTimeout);
    });

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
                error = "Page has expired. Please reload the page and try again.";
            });
        })
        .catch(exception => {
            console.error(exception);
            error = "Page has expired. Please reload the page and try again.";
        });
    }

    function changeSidebarVisibility() {
        for (let i = 0; i < visible.length; i++) visible[i] = false;
        transition = true;
        visibilityTimeout = setTimeout(() => {
            openNavigator = !openNavigator;
            visibilityTimeout = null;
        }, 1);
    }

    function handleLifetimeUpdate() {
        fetch('/update-lifetime', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': otherProps.csrfToken,
            }
        })
        .then(response => {
            response.json().then(data => {
                switch (response.status) {
                    case 200:
                        message = 'Oh, There you are!';
                        messageTimeout = setTimeout(() => {
                            message = '';
                            messageTimeout = null;
                        }, 3000);
                        lifetime = data.lifetime;
                        break;
                    default:
                        console.error(`Unexpected response status ${response.status} with messages:`);
                        console.error(data);
                        break;
                }
            })
            .catch(exception => {
                console.error(exception);
                error = "Page has expired. Please reload the page and try again.";
            });
        })
        .catch(exception => {
            console.error(exception);
            error = "Page has expired. Please reload the page and try again.";
        });
    }
</script>

<svelte:head>
    <title>Matt's Home</title>
    <script src="https://kit.fontawesome.com/0cdd07cc84.js" crossorigin="anonymous"></script>
</svelte:head>

<div class="header flex align-items-center justify-content-space-between">
    {#if Sidebar}
        <!-- svelte-ignore a11y_consider_explicit_label -->
        <button onclick={changeSidebarVisibility} style="all: unset; cursor: pointer; z-index: 9999;">
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
        {#if otherProps.gameInfo?.image && $windowInnerWidth > 600}
            <a use:inertia href="/game/{convertSpaceToUnderscore(otherProps.gameInfo.game)}">
                <img class="game-logo" src="{otherProps.gameInfo.image}" alt="{otherProps.gameInfo.title ?? "game"} logo">
            </a>
        {/if}
        <a use:inertia href="/">
            <div class="{$isMobile ? 'title-4' : 'title-1'}" style="text-align: center;">
                Matt's Game Guides
            </div>
        </a>
    </div>
    {#if otherProps.user}
        <form id="logout" onsubmit={logoutHandler}>
            <button type="submit" style="all: unset; cursor: pointer; font-weight: bold;">
                <div class="{$isMobile ? 'title-5' : 'title-4'}">
                    Logout
                </div>
            </button>
        </form>
    {:else}
        <div style="">
            <a use:inertia href="/login">
                <div class="{$isMobile ? 'title-5' : 'title-4'}">
                    Login
                </div>
            </a>
        </div>
    {/if}
    {#if openNavigator}
        <div class="menu flex column" transition:slide={{axis: 'x'}}>
            <div>
                <Sidebar bind:openNavigator bind:visible {...otherProps} />
            </div>
        </div>
        <!-- svelte-ignore a11y_click_events_have_key_events -->
        <!-- svelte-ignore a11y_no_static_element_interactions -->
        <div class="background" transition:slide={{axis: 'x'}} onclick={changeSidebarVisibility}></div>
    {/if}
</div>

{#if (currentLifetime - currentTime) / 1000 <= 180}
    <div class="flex justify-content-center" in:fade>
        {#if (currentLifetime - currentTime) / 1000 > 0}
            <button onclick={handleLifetimeUpdate} class="lifetime-notification-box" in:fade>
                <div class="title-3">
                    Are you still there?
                </div>
                <div class="title-5">
                    If so, please click on this box to keep your session alive.
                </div>
                <div class="title-6">
                    Session will expire in {parseInt((currentLifetime - currentTime) / 1000)} seconds
                </div>
            </button>
        {:else}
            <button class="lifetime-notification-box" onclick={() => window.location.href = window.location.href} in:fade>
                <div class="title-3">
                    Page has expired.
                </div>
                <div class="title-5">
                    Please reload the page.
                </div>
            </button>
        {/if}
    </div>
{/if}

{#if message}
    <div class="flex justify-content-center" out:fade>
        <div class="lifetime-notification-box">
            <div class="title-3">
                {message}
            </div>
        </div>
    </div>
{/if}

<div class="children-container">
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

    .lifetime-notification-box {
        border: 2px solid blue;
        border-radius: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        background-color: white;
        z-index: 2000;
        position: fixed;
        bottom: 5vh;
        align-self: center;
        justify-self: center;
        padding: 1em;
    }

    .children-container {
        margin: 1em 2em;

        @media screen and (max-width: variables.$mobileVW) {
            margin: 0;
        }
    }

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
            padding: 0 1em;
            gap: 10px;
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
            padding-top: 3em;
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
