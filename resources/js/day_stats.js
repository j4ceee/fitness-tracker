window.onload = function() {
    let progress_bar_cont = document.getElementById('progress_bar_cont');
    let blueProgressBar = document.getElementById('progress_bar_blue');
    let progress = progress_bar_cont.getAttribute('data-progress');
    let mode = progress_bar_cont.getAttribute('data-mode');

    progress = 1 - progress;

    let initial = 198;
    let final = 198 * progress;

    let duration = 1000; // duration of the animation in milliseconds

    // start the animation
    animateProgressBar(initial, final, duration, blueProgressBar);

    if (mode === 'edit') {
        let day_calorie_goal = document.getElementById('day_calorie_goal');
        let calorie_goal_text = document.getElementById('cal_goal_text');
        let day_calorie_progress = document.getElementById('calories');
        let calorie_progress_text = document.getElementById('cal_progress_text');

        day_calorie_progress.addEventListener('input', function() {
            let goalValue = parseFloat(day_calorie_goal.value || day_calorie_goal.placeholder);
            let progressValue = parseFloat(day_calorie_progress.value ?? '0');

            if (isNaN(goalValue) || goalValue === 0) {
                return;
            }

            if (isNaN(progressValue)) {
                day_calorie_progress.value = 0;
                progressValue = 0;
            } else {
                day_calorie_progress.value = progressValue;
            }

            let calProgress = progressValue / goalValue;
            calProgress = Math.min(Math.max(calProgress, 0), 1); // Ensure calProgress is between 0 and 1

            let initial = parseFloat(blueProgressBar.style.strokeDashoffset) || 198;
            let final = 198 * (1 - calProgress);

            progress_bar_cont.setAttribute('data-progress', calProgress);

            // start the animation
            animateProgressBar(initial, final, 1000, blueProgressBar);
            calorie_progress_text.innerHTML = progressValue;
        });

        day_calorie_goal.addEventListener('input', function() {
            let goalValue = parseFloat(day_calorie_goal.value || day_calorie_goal.placeholder);
            let progressValue = parseFloat(day_calorie_progress.value ?? '0');

            if (isNaN(goalValue) || goalValue === 0) {
                return;
            }

            if (isNaN(progressValue)) {
                day_calorie_progress.value = 0;
                progressValue = 0;
            } else {
                day_calorie_progress.value = progressValue;
            }

            let calProgress = progressValue / goalValue;
            calProgress = Math.min(Math.max(calProgress, 0), 1); // Ensure calProgress is between 0 and 1

            let initial = parseFloat(blueProgressBar.style.strokeDashoffset) || 198;
            let final = 198 * (1 - calProgress);

            progress_bar_cont.setAttribute('data-progress', calProgress);

            // start the animation
            animateProgressBar(initial, final, 1000, blueProgressBar);
            calorie_goal_text.innerHTML = goalValue;
        });

        /*
         * Water counter
         */
        let water_plus = document.getElementById('water_btn_plus');
        let water_minus = document.getElementById('water_btn_minus');

        let waterCount = document.getElementById('water_count');
        let waterInput = document.getElementById('water');

        if (parseFloat(waterInput.value) === 3) {
            // set visibility to hidden
            water_plus.style.opacity = '0.5';
            water_plus.style.cursor = 'default';
        }

        if (parseFloat(waterInput.value) === 0) {
            // set visibility to hidden
            water_minus.style.opacity = '0.5';
            water_minus.style.cursor = 'default';
        }

        water_plus.addEventListener('click', function() {
            addToCounter(3, 0.5, waterInput, waterCount, 2);

            if (parseFloat(waterInput.value) === 3) {
                water_plus.style.opacity = '0.5';
                water_plus.style.cursor = 'default';
            }
            if (parseFloat(waterInput.value) > 0) {
                water_minus.style.opacity = '1';
                water_minus.style.cursor = 'pointer';
            }
        });

        water_minus.addEventListener('click', function() {
            subtractFromCounter(0, 0.5, waterInput, waterCount, 2);

            if (parseFloat(waterInput.value) < 3) {
                water_plus.style.opacity = '1';
                water_plus.style.cursor = 'pointer';
            }
            if (parseFloat(waterInput.value) === 0) {
                water_minus.style.opacity = '0.5';
                water_minus.style.cursor = 'default';
            }
        });
    }
}

function addToCounter(maxValue, changeValue, inputElement, counterTextElement, fractionDigits = 0) {
    if (parseFloat(inputElement.value) < maxValue) {
        inputElement.value = parseFloat(inputElement.value) + changeValue;
        if (fractionDigits > 0) {
            counterTextElement.innerHTML = parseFloat(inputElement.value).toFixed(fractionDigits);
        } else {
            counterTextElement.innerHTML = parseFloat(inputElement.value);
        }
    }
}

function subtractFromCounter(minValue, changeValue, inputElement, counterTextElement, fractionDigits = 0) {
    if (parseFloat(inputElement.value) >= minValue + changeValue) {
        inputElement.value = parseFloat(inputElement.value) - changeValue;
        if (fractionDigits > 0) {
            counterTextElement.innerHTML = parseFloat(inputElement.value).toFixed(fractionDigits);
        } else {
            counterTextElement.innerHTML = parseFloat(inputElement.value);
        }
    }
}

function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
}

/**
 * Animate the progress bar
 * @param {number} initial
 * @param {number} final
 * @param {number} duration
 * @param {HTMLElement} progressBar
 */
function animateProgressBar(initial, final, duration, progressBar) {
    let start = null;

    function animate(timestamp) {
        if (!start) start = timestamp;
        let elapsed = timestamp - start;
        let progress = Math.min(elapsed / duration, 1);

        if (progress < 1) {
            let easedProgress = easeOutCubic(progress);
            progressBar.style.strokeDashoffset = initial + (final - initial) * easedProgress;
            window.requestAnimationFrame(animate);
        } else {
            progressBar.style.strokeDashoffset = final;
        }
    }

    window.requestAnimationFrame(animate);
}
