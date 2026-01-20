<script>
    import { inertia, router } from "@inertiajs/svelte";
    import Layout from "../../Components/Layout.svelte";

    let { csrfToken, ...otherProps } = $props();

    let error = $state('');

    function submitHandler(e) {
        e.preventDefault();
        error = '';
        let formData = new FormData(e.target);
        fetch('/login', {
            method: 'POST',
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
            body: formData,
        })
        .then(response => {
            response.json().then(data => {
                switch(response.status) {
                    case 302:
                        router.get(data.redirect);
                        break;
                    case 400:
                        document.getElementById('password').value = '';
                        error = data.error;
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

<Layout {...otherProps}>
    <div class="flex column" style="gap: 0.3em; margin: 0 2em 1em;">
        <div class="title-2">
            Welcome to the login page.
        </div>
        <div class="title-5">
            Don't have an account? Click <a use:inertia href="/register">here</a> to create a new account.
        </div>
    </div>
    {#if error}
        <div style="color: red; font-size: 14px; margin-bottom: 10px; margin-left: 2em;">
            {error}
        </div>
    {/if}
    <form id="login" onsubmit={submitHandler}>
        <div class="flex column" style="max-width: 250px; gap: 1em; margin: 0 2em;">
            <div class="flex column" style="gap: 0.4em;">
                <label class="flex column" for="email">
                    Enter your email:
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <span>Email is not valid.</span>
                </label>
                <label class="flex column" for="password">
                    Enter your password:
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <span>Password is not valid.</span>
                </label>
            </div>
            <button type="submit">Login</button>
        </div>
    </form>
</Layout>

<style lang="scss">
    label {
        font-size: 16px;
    }

    input {
        font-size: 16px;
        padding: 0.3em;
        border-radius: 0.5em;
        border: 1px solid black;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
    }

    span {
        color: red;
        display: none;
    }
    
    label:has(input:user-invalid) {
        input {
            border-color: red;
        }

        span {
            display: block;
        }
    }

    button {
        font-size: 16px;
        align-self: center;
        padding: 0.3em 0.5em;
        border-radius: 0.5em;
        border: 1px solid black;
        width: max-content;
        cursor: pointer;
    }
</style>
