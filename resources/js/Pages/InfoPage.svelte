<script>
    import { onDestroy, onMount } from "svelte";
    import { convertSpaceToUnderscore } from "../helper";
    import { inertia, router } from "@inertiajs/svelte";
    import showdown from "showdown";
    import DOMPurify from "dompurify";
    import TurndownService from "turndown";
    import { gfm } from "turndown-plugin-gfm";

    let {
        csrfToken,
        gameInfo,
        page,
        user,
    } = $props();

    let successTimeout, errorTimeout;
    let message = $state('');
    let error = $state('');
    let editing = $state(false);
    let sections = $state([]);

    onMount(() => {
        sections = page.sections;
    });

    onDestroy(() => {
        clearTimeout(successTimeout);
        clearTimeout(errorTimeout);
    });

    function saveHandler(e) {
        e.preventDefault();
        error = '';
        message = '';
        let formData = new FormData(e.target);

        let newSections = [];
        let deletionKeys = [];
        formData.forEach((value, key) => {
            if (key.includes("section")) {
                let index = parseInt(key.split("_")[1]);
                if (!newSections[index]) newSections[index] = {};
                if (key.includes("title")) newSections[index].title = value;
                else if (key.includes("body")) {
                    newSections[index].body = {
                        type: "text",
                        data: value,
                    };
                }
                deletionKeys.push(key);
            }
        });
        deletionKeys.forEach(key => formData.delete(key));
        newSections.forEach((section, index) => {
            formData.append("sections[" + index + "][title]", section.title);
            formData.append("sections[" + index + "][body][type]", "text");
            let html = convertMarkdownToHTML(section.body.data);
            let turndownService = new TurndownService({
                headingStyle: 'atx',
                codeBlockStyle: 'fenced',
                bulletListMarker: '-',
            });
            turndownService.use(gfm);
            try {
                const markdown = turndownService.turndown(html);
                formData.append("sections[" + index + "][body][data]", markdown);
            }
            catch (exception) {
                console.error(exception);
                error = "Unable to save due to failed markdown parse.";
                return;
            }
        });

        let fetchString = "/game/edit/" + convertSpaceToUnderscore(page.game);
        if (page.subtitle) fetchString += "/" + convertSpaceToUnderscore(page.subtitle);
        if (page.page) fetchString += "/" + convertSpaceToUnderscore(page.page);
        fetch(fetchString, {
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
                        message = data.message + 'Reloading...';
                        successTimeout = setTimeout(() => {
                            message = '';
                            window.location.href = window.location.href;
                            successTimeout = null;
                        }, 1000);
                        window.scrollTo(0, 0);
                        editing = false;
                        break;
                    case 302:
                        message = data.message;
                        successTimeout = setTimeout(() => {
                            message = '';
                            successTimeout = null;
                        }, 10000);
                        window.scrollTo(0, 0);
                        editing = false;
                        router.get(data.redirect);
                        break;
                    case 400:
                    case 403:
                    case 404:
                        error = data.error;
                        errorTimeout = setTimeout(() => {
                            error = '';
                            errorTimeout = null;
                        }, 20000);
                        window.scrollTo(0, 0);
                        break;
                    default:
                        console.error(`Unexpected response status ${response.status} with messages:`);
                        console.error(data);
                        break;
                }
            })
            .catch(exception => {
                console.error(exception);
            });
        })
        .catch(exception => {
            console.error(exception);
        });
    }

    function sectionHandler(e) {
        e.preventDefault();
        if (e.target.id.includes("add")) {
            if (e.target.id.includes("between")) {
                let index = parseInt(e.target.id.split("_")[3]);
                sections.splice(index + 1, 0, {
                    title: "",
                    body: {
                        type: "text",
                        data: "",
                    }
                });
            }
            else {
                sections.push({
                    title: "",
                    body: {
                        type: "text",
                        data: "",
                    }
                });
            }
        }
        else {
            let index = parseInt(e.target.id.split("_")[1]);
            sections.splice(index, 1);
        }
    }

    function convertMarkdownToHTML(markdown) {
        let converter = new showdown.Converter();
        converter.setFlavor('github');
        let dirtyHTML = converter.makeHtml(markdown);
        let html = DOMPurify.sanitize(dirtyHTML);
        return html;
    }
</script>

<div class="flex column align-items-center">
    {#if message}
        <div class="title-2" style="color: green; max-width: 600px;">{message}</div>
    {/if}
    {#if error}
        <div class="title-2" style="color: red; max-width: 600px;">{error}</div>
    {/if}
    {#if editing}
        <form id="save" onsubmit={saveHandler} class="form flex column justify-content-center align-items-center">
            <div>
                <label for="title">Title: </label>
                <input type="text" id="title" name="title" defaultValue="{page.page ?? page.subtitle ?? page.game}" disabled={!page.subtitle && !page.page}>
            </div>
            <hr>
            <div class="flex column">
                {#each sections as section, index}
                    {#if section.title != page.sections[0].title}
                        <hr>
                        <button id="remove_{index}_section" name="remove_{index}_section" type="button" onclick={sectionHandler}>
                            <i class="fa-solid fa-minus"></i>
                            Remove Section {index + 1}
                        </button>
                    {/if}
                    <div class="flex column">
                        <div class="title-1">
                            <label for="section_{index}_title">Section {index + 1} Title: </label>
                            <input style="width: 65%; min-width: 250px;" type="text" id="section_{index}_title" name="section_{index}_title" defaultValue="{section.title}">
                        </div>
                        {#if section.body.type == "text"}
                            <div class="title-6">
                                <label for="section_{index}_body">Section {index + 1} Body: </label>
                                <textarea rows="7" id="section_{index}_body" name="section_{index}_body" defaultValue="{section.body.data}"></textarea>
                            </div>
                        {/if}
                    </div>
                    <button id="add_section_between_{index}" name="add_section" type="button" onclick={sectionHandler}>
                        <i class="fa-solid fa-plus"></i>
                        Add Section After {index + 1}
                    </button>
                {/each}
            </div>
            <button type="submit" style="all: unset; cursor: pointer; font-weight: bold;">
                Save
            </button>
        </form>
    {:else}
        <div class="flex justify-content-center align-items-center">
            <div class="title-1">
                {page.page ?? page.subtitle ?? page.game}
            </div>
            {#if user?.roles?.includes("admin")}
                <button onclick={() => editing = true} class="edit-button">
                    <i class="fa-solid fa-pencil"></i>
                    <div>Edit</div>
                </button>
            {/if}
        </div>
        <hr>
        <div class="flex column" style="max-width: 1200px;">
            {#each page.sections as section}
                {#if section.title != page.sections[0].title}
                    <hr>
                {/if}
                <div class="flex column">
                    <div class="title-1">
                        {section.title}
                    </div>
                    {#if section.body.type == "text"}
                        <div class="title-6 markdown flex column">
                            {@html convertMarkdownToHTML(section.body.data)}
                        </div>
                    {/if}
                </div>
            {/each}
        </div>
        <hr>
        <div class="flex align-items-center justify-content-center" style="max-width: 95vw;">
            <table>
                <thead>
                    <tr>
                        <th>Page Links</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="flex justify-content-center align-items-center title-4">
                                <a use:inertia href="/game/{convertSpaceToUnderscore(gameInfo.game)}">{gameInfo.game}</a>
                            </div>
                        </td>
                    </tr>
                    {#each gameInfo.sections as section}
                        <tr>
                            <td>
                                <div class="flex column justify-content-center align-items-center">
                                    <div>
                                        <a use:inertia class="title-4" use:inertia href="/game/{convertSpaceToUnderscore(gameInfo.game)}/{convertSpaceToUnderscore(section.subtitle)}">{section.subtitle}</a>
                                    </div>
                                    <div class="flex flex-wrap justify-content-center align-items-center" style="gap: 0.5em;">
                                        {#each section.sections as subSection}
                                            <a use:inertia class="title-6" style="margin: 0.1em 0;" use:inertia href="/game/{convertSpaceToUnderscore(gameInfo.game)}/{convertSpaceToUnderscore(section.subtitle)}/{convertSpaceToUnderscore(subSection)}">{subSection}</a>
                                        {/each}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
        </div>
    {/if}
</div>

<style lang="scss">
    @use "../../css/variables";

    .title-1 {
        margin: 0.5em 1em;
    }

    .title-6 {
        margin: 0.5em 2em;
        font-size: 16px;
        // white-space: pre-line;
    }

    .markdown {
        :global(p) {
            margin: 0.5em 0;
        }

        :global(table) {
            border: 1px solid black;
            border-collapse: collapse;
            width: max-content;
        }

        :global(th) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        :global(th), :global(td), :global(tr) {
            padding: 0.1em 0.3em;
            border: 1px solid black;
        }
    }

    hr {
        width: 95%;
    }

    button {
        background-color: white;
        border: none;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        gap: 5px;
    }

    .form {
        gap: 1em;
        max-width: 1200px;
        width: 100%;
    }

    input {
        font-size: 18px;
    }

    textarea {
        width: 80vw;
        height: fit-content;
        resize: vertical;

        @media screen and (max-width: variables.$mobileVW) {
            width: 600px;
            max-width: 90vw;
        }
    }

    table, td, th {
        border-collapse: collapse;
        padding: 0.1em;
        border: 1px solid black;
    }
</style>
