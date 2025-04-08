// Confrim password (pārbaudīt vai pareizi ievadīja paroli)
document.addEventListener("DOMContentLoaded", function () {
    const password = document.getElementById("pswd");
    const confirmPassword = document.getElementById("confirm_pswd");
    const submitButton = document.getElementById("pieslegt");

    // Create a message element
    const message = document.createElement("div");
    message.style.color = "red";
    message.style.fontSize = "14px";
    confirmPassword.insertAdjacentElement("afterend", message);

    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            message.textContent = "Paroles nesakrīt!";
            submitButton.disabled = true;
        } else {
            message.textContent = "";
            submitButton.disabled = false;
        }
    }

    confirmPassword.addEventListener("input", validatePassword);
});
