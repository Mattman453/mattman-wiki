<script>
    import Layout from "../Components/Layout.svelte";

</script>

<Layout>
    <div class="text-block flex justify-content-center align-items-center">
        <div class="text">
            <div class="opening">
                My name is Matt Cruickshank. I am a Computer Engineering Graduate coming from the University of Guelph. I have enjoyed programming since before starting High School and enjoy both gaming and software development.
            </div>
            <div class="body">
                Since I was a kid, I have enjoyed playing video games whether they be platformers, rhythmn games, FPSs, or incremental type games. Each has shown me the breadth of playstyles out there.
            </div>
            <div class="body">
                One of my favourtie games to come back to is Realm Grinder, optimizing how quickly I can get through each reincarnation. Though I have absolutely cheated using offline mode and an autoclicker to make things go by faster.
            </div>
            <div class="body">
                The second best game I come back to is Assassin's Creed IV: Black Flag, going around the Carribean doing everything I can to take down ships with no damage taken.
            </div>
        </div>
        <img src="/About/Headshot.jpeg" alt="Matt's Face">
    </div>
    
    <div class="text-block flex justify-content-center align-items-center">
        <img src="/About/phpProgramming.png" alt="Block of Random Code">
        <div class="text">
            <div class="opening">
                When it comes to programming, I have had the pleasure of working on Web Development projects such as this made with Laravel & Svelte as well as for companies in the manufacturing industry.
            </div>
            <div class="body">
                I have also had the pleasure of working on bare-metal systems using Assembly and C. This is with both standard processors and FPGAs that emulate processors.
            </div>
            <div class="body">
                During my time as a student, I also worked with languages such as Java and Python to create applications for XML manipulation and multithreaded data processing.
            </div>
        </div>
    </div>
    
    <div class="text-block flex column align-items-center" style="margin-bottom: 0.5em;">
        <div class="text">
            <div class="opening">
                This website was made using Laravel for the backend and Svelte for the frontend. Any data used is hosted on MongoDB.
            </div>
            <div class="body">
                The website's code, except for the data held in MongoDB, is available on GitHub <a href="https://github.com/Mattman453/mattman-wiki" target="_blank" rel="noopener noreferrer">Here</a> or by clicking on the GitHub logo below. The only data on MongoDB is the content of each page.
            </div>
        </div>
    </div>
    <a href="https://github.com/Mattman453/mattman-wiki" target="_blank" rel="noopener noreferrer" alt="GitHub Logo" aria-label="GitHub Project Link" class="github-logo flex justify-content-center">
        <svg aria-hidden="true" viewBox="0 0 24 24" version="1.1" data-view-component="true">
            <path d="M12 1C5.923 1 1 5.923 1 12c0 4.867 3.149 8.979 7.521 10.436.55.096.756-.233.756-.522 0-.262-.013-1.128-.013-2.049-2.764.509-3.479-.674-3.699-1.292-.124-.317-.66-1.293-1.127-1.554-.385-.207-.936-.715-.014-.729.866-.014 1.485.797 1.691 1.128.99 1.663 2.571 1.196 3.204.907.096-.715.385-1.196.701-1.471-2.448-.275-5.005-1.224-5.005-5.432 0-1.196.426-2.186 1.128-2.956-.111-.275-.496-1.402.11-2.915 0 0 .921-.288 3.024 1.128a10.193 10.193 0 0 1 2.75-.371c.936 0 1.871.123 2.75.371 2.104-1.43 3.025-1.128 3.025-1.128.605 1.513.221 2.64.111 2.915.701.77 1.127 1.747 1.127 2.956 0 4.222-2.571 5.157-5.019 5.432.399.344.743 1.004.743 2.035 0 1.471-.014 2.654-.014 3.025 0 .289.206.632.756.522C19.851 20.979 23 16.854 23 12c0-6.077-4.922-11-11-11Z" />
        </svg>
    </a>
</Layout>

<style lang="scss">
    .text-block {
        margin: 2em;

        .text {
            max-width: 600px;
            margin: 0 1em;

            * {
                margin: 0.5em 0;
            }

            .opening {
                font-size: 20px;
            }

            .body {
                font-size: 16px;
            }
        }

        img {
            min-width: 120px;
            max-width: 300px;
            min-height: 200px;
            max-height: 500px;
            border-radius: 1em;
            margin: 0 1em;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
    }

    .github-logo svg {
        min-width: 50px;
        max-width: 200px;
        min-height: 50px;
        max-height: 200px;
    }
</style>
