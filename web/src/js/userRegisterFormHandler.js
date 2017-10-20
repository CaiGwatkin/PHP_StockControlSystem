/**
 * User Registration Form Handler file.
 *
 * Handles user input validation using javascript, AJAX, and JQuery.
 */


/**
 * The username input element.
 * @type {*|jQuery|HTMLElement}
 */
var usernameInput = null;

/**
 * The username error td element.
 * @type {*|jQuery|HTMLElement}
 */
var usernameError = null;

/**
 * The password input element.
 * @type {*|jQuery|HTMLElement}
 */
var passwordInput = null;

/**
 * The password error td element.
 * @type {*|jQuery|HTMLElement}
 */
var passwordError = null;

/**
 * The password repeat input element.
 * @type {*|jQuery|HTMLElement}
 */
var passwordRepeatInput = null;

/**
 * The password repeat error td element.
 * @type {*|jQuery|HTMLElement}
 */
var passwordRepeatError = null;

/**
 * Returns true if string is alphanumeric.
 *
 * @returns {boolean} true if string is alphanumeric.
 */
String.prototype.isAlphanumeric = function() {

    return !!this.match(/^[a-zA-Z0-9]+$/);
};

/**
 * Once HTML document is fully loaded, setup event handlers.
 */
$(document).ready(function () {
    // Find elements with JQuery.
    usernameInput = $("input[name=username]");
    usernameError = usernameInput.siblings().find('.error');
    passwordInput = $("input[name=password]");
    passwordError = passwordInput.siblings().find('.error');
    passwordRepeatInput = $("input[name=passwordRepeat]");
    passwordRepeatError = passwordRepeatInput.siblings().find('.error');

    // Add listeners
    usernameInput.keyup(checkUsername);
    passwordInput.keyup(checkPassword);
    passwordRepeatInput.keyup(checkPasswordRepeat);
});

/**
 * Check that username is valid and update error message.
 */
function checkUsername() {

    usernameError.html(checkUsernameValid());
    if (usernameError.html() === "") {
        checkUsernameUnique();
    }
}

/**
 * Check that password is valid and update error message.
 */
function checkPassword() {

    passwordError.html(checkPasswordValid());
    if (passwordRepeatError.html() !== "") {
        passwordRepeatError.html(checkPasswordsMatch());
    }
}

/**
 * Check that password repeat is valid and update error message.
 */
function checkPasswordRepeat() {

    passwordRepeatError.html(checkPasswordsMatch());
}

/**
 * Checks that the username is valid.
 *
 * @returns {string} The message to be displayed.
 */
function checkUsernameValid() {

    var username = usernameInput.val(),
        message;
    if (!username.isAlphanumeric() && username.length !== 0) {
        message = "Invalid username: must contain alphanumeric characters only";
    }
    else {
        message = "";
    }
    return message;
}

/**
 * Checks that the username is unique in the database.
 */
function checkUsernameUnique() {

    $.ajax({
        type: "POST",
        url: "/js/verifyRegistrationForm",
        data: { "username": usernameInput.val() },
        dataType: "text",
        success: [
            function (data) {
                if (data === "duplicate") {
                    usernameError.html("Invalid username: username already exists");
                }
            }
        ]
    });
}

/**
 * Checks that the password is valid.
 *
 * @returns {string} The message to be displayed.
 */
function checkPasswordValid() {

    var password = passwordInput.val(),
        message;
    if (!!password.match(/^(?=.*[A-Z])(^[a-zA-Z0-9]{7,15}$)/)) {
        message = "";
    }
    else {
        message = "Invalid password: password must be between 7 and 15 alphanumeric characters and "+
            "contain at least one uppercase letter (no special characters allowed)";
    }
    return message;
}

/**
 * Checks that the password is valid.
 *
 * @returns {string} The message to be displayed.
 */
function checkPasswordsMatch() {

    var message;
    if (passwordInput.val() === passwordRepeatInput.val()) {
        message = "Passwords match!";
    }
    else {
        message = "Invalid password: passwords do not match";
    }
    return message;
}
