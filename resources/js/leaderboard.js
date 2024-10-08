window.onload = function () {

    /* code from https://codepen.io/Johnny-Dragovic/pen/QWXOgop */

    let data;

    let lb_data = document.getElementById("leaderboard_array");
    if (lb_data) {
        data = JSON.parse(lb_data.value);
    }

    let container = document.querySelector(".leaderboard_container");
    let user_temp = document.getElementById("user_temp"); // template for each user

    let total_button = document.getElementById("sort_total");
    let month_button = document.getElementById("sort_month");
    let day_button = document.getElementById("sort_day");

    function renderItems() {
        // count the number of items in the data array
        let count = data.length;
        container.style.setProperty("--team-count", count);

        data.forEach((el, i) => {
            let box = user_temp.content.cloneNode(true);
            container.appendChild(box);
            box = container.lastElementChild;
            box.style.setProperty("--i", i);

            let name = box.querySelector(".name");
            name.innerHTML = el.name;

            let total_score = box.querySelector(".total_score");
            total_score.innerHTML = el.total_score;

            let month_score = box.querySelector(".month_score");
            month_score.innerHTML = el.month_score;

            let day_score = box.querySelector(".day_score");
            day_score.innerHTML = el.day_score;

            let rank = box.querySelector(".rank");
            rank.innerHTML = i + 1;

            let calendarLink = box.querySelector(".team_calendar_link");
            calendarLink.href = el.days_index_url;
            let calendarIcon = box.querySelector(".team_calendar_icon");
            // set title & alt text for calendar icon
            calendarIcon.title = "Kalender von " + el.name + " anzeigen";
            calendarIcon.alt = "Kalender von " + el.name + " anzeigen";
        });
    }

    function sortAndRank(sortBy) {
        data.sort((a, b) => {
            if (b[sortBy] === a[sortBy]) {
                return a.name.localeCompare(b.name);
            }
            return b[sortBy] - a[sortBy];
        });

        let displayRank = 1;
        let prevScore = null;

        data.forEach((e, i) => {
            e.position = i; // Internal position for CSS ordering
            if (e[sortBy] !== prevScore) {
                displayRank = i + 1;
            }
            e.displayRank = displayRank;
            prevScore = e[sortBy];
        });

        updateRanks(sortBy);
    }

    function updateRanks(mode = "total_score") {
        container.setAttribute("data-sort", mode);
        total_button.setAttribute("data-sort", mode === "total_score" ? "desc" : "");
        month_button.setAttribute("data-sort", mode === "month_score" ? "desc" : "");
        day_button.setAttribute("data-sort", mode === "day_score" ? "desc" : "");

        let firstPlaces = 0;

        let AllTeams = Array.from(document.querySelectorAll(".team"));
        AllTeams.forEach((element) => {
            let elementName = element.querySelector(".name");
            let rank = element.querySelector(".rank");

            let score = element.querySelector(`.${mode}`).innerHTML;

            let teamData = data.find((team) => team.name === elementName.innerHTML);
            let displayRank = teamData.displayRank;
            let position = teamData.position;

            element.classList.remove("plc-1", "plc-2", "plc-3", "plc-4");

            if (displayRank <= 4 && score !== "0") {
                element.classList.add(`plc-${displayRank}`);

                if (displayRank === 1) {
                    firstPlaces++;
                }
            }

            element.style.setProperty("--i", position);
            rank.innerHTML = displayRank;
        });

        container.style.setProperty("--first-places", firstPlaces);
    }

    renderItems();

    setTimeout(() => {
        sortAndRank("total_score");
    }, 100);

    total_button.addEventListener("click", () => {
        sortAndRank("total_score");
    });

    month_button.addEventListener("click", () => {
        sortAndRank("month_score");
    });

    day_button.addEventListener("click", () => {
        sortAndRank("day_score");
    });
}
