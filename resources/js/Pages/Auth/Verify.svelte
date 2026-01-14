<script>
    import { onDestroy, onMount } from "svelte";
    import Layout from "../../Components/Layout.svelte";

    let { verificationSentAt, throttle, csrfToken, ...otherProps } = $props();

    let newestVerificationTime = $state(new Date(verificationSentAt));
    let error = $state('');
    let message = $state('');
    let currentDate = $state(new Date());

    let dateInterval;
    let messageTimeout;

    onMount(() => {
        dateInterval = setInterval(() => {
            currentDate = new Date();
        }, 100);
    });

    onDestroy(() => {
        clearInterval(dateInterval);
        clearTimeout(messageTimeout);
    });

    function submitHandler(e) {
        e.preventDefault();
        fetch('/send-verification', {
            method: 'POST',
            headers: {
                "X-CSRF-TOKEN": csrfToken,
            },
            body: new FormData(e.target),
        })
        .then(response => {
            response.json().then(data => {
                switch(response.status) {
                    case 200:
                        newestVerificationTime = new Date();
                        error = '';
                        message = data.success;
                        messageTimeout = setTimeout(() => {
                            message = '';
                        }, 5000);
                        break;
                    case 302:
                        window.location.href = data.redirect;
                        error = '';
                        break;
                    case 400:
                        error = data.error;
                        break;
                    case 500:
                        error = data.error;
                        console.error(data);
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
        });
    }
</script>

<Layout {...otherProps}>
    <div class="flex column justify-content-center align-items-center" style="gap: 0.5em;">
        <div class="title-2">Your account has been created. You should receive an email shortly with a link to verify.</div>
        <div class="title-3">If it does not arrive within 3 minutes, press the button below to resend.</div>
        <div style:color="red">{error}</div>
        <div class="title-6">{message}</div>
        <form id="send-verification" onsubmit={submitHandler}>
            <button type="submit" disabled={currentDate - newestVerificationTime < (throttle * 1000)}>
                {throttle - ((currentDate - newestVerificationTime) / 1000) > 0 ? 
                    parseInt(throttle - ((currentDate - newestVerificationTime) / 1000)) : 
                    'Resend'
                }
            </button>
        </form>
    </div>
</Layout>

<style lang="scss">
    button {
        font-size: 16px;
        align-self: center;
        padding: 0.5em 3em;
        background-color: aquamarine;
        border-radius: 0.5em;
        border: 1px solid black;
        width: max-content;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }
</style>
