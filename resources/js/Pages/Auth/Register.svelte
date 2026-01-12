<script>
    import { inertia } from "@inertiajs/svelte";
    import Layout from "../../Components/Layout.svelte";

    let { csrfToken, ...otherProps } = $props();

    let error = $state('');

    function submitHandler(e) {
        e.preventDefault();
        error = '';
        let formData = new FormData(e.target);

        if (formData.get('email') !== formData.get('emailConfirm')) {
            error = 'Emails do not match. Please make sure they are the same.';
            return;
        }
        if (formData.get('password') !== formData.get('passwordConfirm')) {
            error = 'Passwords do not match. Please make sure they are the same.';
            return;
        }
        formData.delete('emailConfirm');
        formData.delete('passwordConfirm');

        fetch('/register', {
            method: 'POST',
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
            body: formData,
        })
        .then(response => {
            response.json().then(data => {
                switch(response.status) {
                    case 200:
                        fetch('/verify');
                        break;
                    case 302:
                        window.location.href = data.redirect;
                        break;
                    case 400:
                        error = data.error;
                        break;
                    case 500:
                        error = data.error;
                        console.error(data);
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
            Welcome to the registration page. You only need an account if you plan to edit or work on any pages.
        </div>
        <div class="title-3">
            You do not require an account to view any information on this website.
        </div>
        <div class="title-5">
            Already have an account? Click <a use:inertia href="/login">here</a> to log in with your existing credentials.
        </div>
    </div>
    {#if error}
        <div style="color: red; font-size: 14px; margin-bottom: 10px; margin-left: 2em;">
            {error}
        </div>
    {/if}
    <form id="register" onsubmit={submitHandler}>
        <div class="flex column" style="max-width: 250px; gap: 0.7em; margin: 0 2em;">
            <div class="flex column" style="gap: 5px;">
                <label class="flex column" for="email">
                    Enter your email:
                    <input type="email" id="email" name="email" placeholder="Email" required>
                    <span>Email is not valid.</span>
                </label>
                <label class="flex column" for="emailConfirm">
                    Confirm your email:
                    <input type="email" id="emailConfirm" name="emailConfirm" placeholder="Confirm Email" required>
                    <span>Email is not valid.</span>
                </label>
            </div>
            <div class="flex column" style="gap: 5px;">
                <label class="flex column" for="password">
                    Enter your password:
                    <input type="password" id="password" name="password" placeholder="Password" minlength="8" maxlength="30" required>
                    <span>Password is not valid. Password must be between 8 and 30 characters.</span>
                </label>
                <label class="flex column" for="passwordConfirm">
                    Confirm your password:
                    <input type="password" id="passwordConfirm" name="passwordConfirm" placeholder="Confirm Password" minlength="8" maxlength="30" required>
                    <span>Password is not valid.</span>
                </label>
            </div>
            <button type="submit">Register</button>
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
