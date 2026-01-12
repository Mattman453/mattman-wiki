<script>
    import { inertia } from "@inertiajs/svelte";
    import { isMobile } from "../stores";

    let { children, ...otherProps } = $props();

    let error = $state('');

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
                        window.location.href = data.redirect;
                        break;
                    default:
                        console.error('Unexpected response status ${response.status} with messages:');
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

<div class="header flex align-items-center {otherProps.user ? 'justify-content-space-between' : 'justify-content-center'}">
    {#if otherProps.user}
        <div style="order: 1; color: red;">
            {error}
        </div>
        <form id="logout" onsubmit={logoutHandler}  style="order: 3;">
            <button type="submit" style="all: unset; cursor: pointer; font-weight: bold;">
                Logout
            </button>
        </form>
    {/if}
    <div class="flex" style="order: 2;">
        {#if otherProps.gameInfo?.image}
            <img class="game-logo" src="{otherProps.gameInfo.image}" alt="{otherProps.gameInfo.title ?? "game"} logo">
        {/if}
        <a use:inertia href="/">
            <div class="{$isMobile ? 'title-3' : 'title-1'}">
                Matt's Game Guides
            </div>
        </a>
    </div>
</div>

<div style="margin: 2em;">
    {@render children()}
</div>

<hr>
<div class="footer flex justify-content-center">
    <a use:inertia href="/about">
        <div class="title-3">
            About
        </div>
    </a>
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
        z-index: 9999;
        background-color: white;
        gap: 20px;
        padding: 0 2em;

        @media screen and (max-width: variables.$mobileVW) {
            height: 2.5em;
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
