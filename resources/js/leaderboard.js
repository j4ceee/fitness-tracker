window.onload = function () {

    /* code from https://codepen.io/Johnny-Dragovic/pen/QWXOgop */

    let data = [
        {rank: 0, name: "dog", total_score: 10, month_score: 20},
        {rank: 0, name: "horse", total_score: -3, month_score: 5},
        {rank: 0, name: "dove", total_score: 50, month_score: 8},
        {rank: 0, name: "cat", total_score: 0, month_score: 0},
    ];

    let container = document.querySelector(".leaderboard_container");
    let user_temp = document.getElementById("user_temp"); // template for each user

    let total_button = document.getElementById("sort_total");
    let month_button = document.getElementById("sort_month");

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

            let rank = box.querySelector(".rank");
            rank.innerHTML = i + 1;
        });
    }

    function sortByTotal() {
        data.sort((a, b) => {
            if (b.total_score === a.total_score) {
                return a.name.localeCompare(b.name);
            }
            return b.total_score - a.total_score;
        }).forEach((e, i) => {
            e.rank = i;
        });

        updateRanks("total");
    }

    function sortByMonth() {
        data.sort((a, b) => {
            if (b.month_score === a.month_score) {
                return a.name.localeCompare(b.name);
            }
            return b.month_score - a.month_score;
        }).forEach((e, i) => {
            e.rank = i;
        });

        updateRanks("month");
    }

    function updateRanks(mode = "total") {
        let AllTeams = Array.from(document.querySelectorAll(".team"));
        AllTeams.forEach((element) => {
            let elementName = element.querySelector(".name");
            let rank = element.querySelector(".rank");

            let score;
            if (mode === "total") {
                score = element.querySelector(".total_score").innerHTML;

                // add data-sort attribute to the element
                total_button.setAttribute("data-sort", "desc");
                month_button.removeAttribute("data-sort");
            }
            else {
                score = element.querySelector(".month_score").innerHTML;

                total_button.removeAttribute("data-sort");
                month_button.setAttribute("data-sort", "desc");
            }

            let newRank = data.find(
                (team) => team.name === elementName.innerHTML
            ).rank;

            element.classList.remove("plc-1", "plc-2", "plc-3", "plc-4");

            if (newRank === 0 && score !== "0") {
                element.classList.add("plc-1");
            }
            else if (newRank === 1 && score !== "0") {
                element.classList.add("plc-2");
            }
            else if (newRank === 2 && score !== "0") {
                element.classList.add("plc-3");
            }
            else if (newRank === 3 && score !== "0") {
                element.classList.add("plc-4");
            }

            element.style.setProperty("--i", newRank);
            rank.innerHTML = newRank + 1;
        });
    }

    renderItems();

    setTimeout(() => {
        sortByTotal();
    }, 100);

    total_button.addEventListener("click", () => {
        sortByTotal();
    });

    month_button.addEventListener("click", () => {
        sortByMonth();
    });
}
