const form  = document.getElementsByTagName('form')[0];

const login = document.getElementById("login");
const loginError = document.querySelector('#login + span.error');

const loginRegExp = /^[a-zA-Z0-9]+$/;

login.addEventListener('input', () => {
    // Каждый раз, когда пользователь что-то вводит,
    // мы проверяем, являются ли поля формы валидными

    if (login.validity.valid && !showError()) {
        // Если на момент валидации какое-то сообщение об ошибке уже отображается,
        // если поле валидно, удаляем сообщение
        loginError.textContent = ''; // Сбросить содержимое сообщения
        loginError.className = 'error'; // Сбросить визуальное состояние сообщения
    } else {
        console.log("2")
        showError();
    }
});

form.addEventListener('submit', function (event) {

    if(!login.validity.valid) {
        showError();
        event.preventDefault();
    }
});

function showError() {
    if(login.validity.valueMissing) {
        // Если поле пустое,
        // отображаем следующее сообщение об ошибке
        loginError.textContent = 'You need to enter an e-mail address.';
        return true;
    } else if(login.validity.typeMismatch) {
        // Если поле содержит не email-адрес,
        // отображаем следующее сообщение об ошибке
        loginError.textContent = 'Entered value needs to be an e-mail address.';
        return true;
    } else if(login.validity.tooShort) {
        // Если содержимое слишком короткое,
        // отображаем следующее сообщение об ошибке
        loginError.textContent = `Логин должен содержать минимум ${ login.minLength }`;
        return true;
    }
    if (!loginRegExp.test(login.value)) {
        console.log(loginRegExp.test(login.value))
        loginError.textContent = 'Логин содержит недопустимые символы';
        return true;
    }

    // Задаём соответствующую стилизацию
    loginError.className = 'error active';
}