
window.onload = function() {
    // scroll to top of page
    window.scrollTo(0, 0);

    let genderDropdown = document.getElementById('gender');
    let globalCalGoal = document.getElementById('cal_goal');

    genderDropdown.addEventListener('change', function() {
        let currentCalGoal;

        if (globalCalGoal.value === '') {
            currentCalGoal = 0;
        } else {
            currentCalGoal = parseInt(globalCalGoal.value);
        }

        setCalGoal(genderDropdown.value, globalCalGoal, currentCalGoal);
    });
}

/**
 *
 * @param {string} gender
 * @param {HTMLElement} globalCalGoal
 * @param {number} currentCalGoal
 */
function setCalGoal(gender, globalCalGoal, currentCalGoal) {
    let validValues = [2500, 2000, 2250, 0]

    if (gender === 'm' && validValues.includes(currentCalGoal)) {
        globalCalGoal.value = 2500
    }
    else if (gender === 'f' && validValues.includes(currentCalGoal)) {
        globalCalGoal.value = 2000
    }
    else if (validValues.includes(currentCalGoal)){
        globalCalGoal.value = 2250
    }
}
