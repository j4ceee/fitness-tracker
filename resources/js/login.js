window.onload = function () {
    let loginButton = document.getElementById('login_btn');
    let loginInputs = document.getElementById('login_inputs');

    let inputs = loginInputs.querySelectorAll('input');

    let clickHandler = (event) => {
        event.preventDefault();

        loginInputs.removeAttribute('data-hidden');
        loginInputs.setAttribute('aria-hidden', 'false');

        // enable inputs
        inputs.forEach(input => {
            input.removeAttribute('disabled');
        });

        loginButton.removeEventListener('click', clickHandler);
    };

    loginButton.addEventListener('click', clickHandler);
}
