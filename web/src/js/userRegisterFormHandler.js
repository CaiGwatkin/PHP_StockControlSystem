/**
 * User Registration Form Handler file.
 *
 * Handles user input validation using javascript, AJAX, and JQuery.
 */

/**
 * Once HTML document is fully loaded, setup event handlers.
 */
$(document).ready(function () {
    var usernameInput = $("input[name=username]"),
        usernameAlertTD = $("#username_alert"),
        passwordInput = $("input[name=password]"),
        passwordAlertTD = $("#password_alert"),
        passwordRepeatInput = $("input[name=passwordRepeat]"),
        passwordRepeatAlertTD = $("#passwordRepeat_alert");

    usernameInput.keyup(function () {

        checkUsernameValid(usernameInput, usernameAlertTD);
        checkUsernameUnique(usernameInput, usernameAlertTD);
    });

    passwordInput.keyup(function () {

        checkPasswordValid(passwordInput, passwordAlertTD);
        if (passwordRepeatAlertTD.html() !== "") {
            checkPasswordsMatch(passwordInput, passwordRepeatInput, passwordRepeatAlertTD);
        }
    });

    passwordRepeatInput.focusout(function () {

        checkPasswordsMatch(passwordInput, passwordRepeatInput, passwordRepeatAlertTD);
    });

    passwordRepeatInput.focusin(function () {
       passwordRepeatAlertTD.html("");
    });
});

/**
 * Checks that the username is valid.
 * 
 * @param usernameInput The username HTML input element.
 * @param usernameAlertTD The username alert HTML td element.
 */
function checkUsernameValid(usernameInput, usernameAlertTD) {

    var username = usernameInput.val(),
        message;
    if (!isAlphanumeric(username) && username.length !== 0) {
        message = "Invalid username: must contain alphanumeric characters only";
    }
    else {
        message = "";
    }
    usernameAlertTD.html(message);
}


/**
 * Checks that the username is unique in the database.
 *
 * @param usernameInput The username HTML input element.
 * @param usernameAlertTD The username alert HTML td element.
 */
function checkUsernameUnique(usernameInput, usernameAlertTD) {

    $.ajax({
        type: "POST",
        url: "/js/verifyRegistrationForm",
        data: { "username": usernameInput.val() },
        dataType: "text",
        success: [
            function (data) {
                if (data === "duplicate") {
                    usernameAlertTD.html("Invalid username: username already exists");
                }
            }
        ]
    });
}

/**
 * Checks that the password is valid.
 *
 * @param passwordInput The password HTML input element.
 * @param passwordAlertTD The password alert HTML td element.
 */
function checkPasswordValid(passwordInput, passwordAlertTD) {

    var password = passwordInput.val(),
        length = password.length,
        message;
    if (length > 7 && length < 15 && !!password.match(/^(?=.*[A-Z])/) && isAlphanumeric(password)) {
        message = "";
    }
    else {
        message = "Invalid password: password must be between 7 and 15 (exclusive) alphanumeric characters and "+
            "contain at least one uppercase letter (no special characters allowed)";
    }
    passwordAlertTD.html(message);
}


/**
 * Checks that the password is valid.
 *
 * @param passwordInput The password HTML input element.
 * @param passwordRepeatInput The password repeat HTML input element.
 * @param passwordRepeatAlertTD The password repeat alert HTML td element.
 */
function checkPasswordsMatch(passwordInput, passwordRepeatInput, passwordRepeatAlertTD) {

    var message;
    if (passwordInput.val() === passwordRepeatInput.val()) {
        message = "Passwords match!";
    }
    else {
        message = "Invalid password: passwords do not match";
    }
    passwordRepeatAlertTD.html(message);
}

/**
 * Returns true if input is alphanumeric.
 *
 * @param input An input string.
 * @returns {boolean} true if input string is alphanumeric.
 */
function isAlphanumeric(input) {

    return !!input.match(/^[a-zA-Z0-9]+$/);
}
