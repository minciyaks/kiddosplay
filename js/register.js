document.addEventListener("DOMContentLoaded", function () {
  const passwordField = document.getElementById("registerPassword");
  const toggleIcon = document.getElementById("toggleRegisterPassword");

  toggleIcon.addEventListener("click", function () {
    const isPassword = passwordField.type === "password";
    passwordField.type = isPassword ? "text" : "password";
    toggleIcon.src = isPassword ? "images/eye-slash.png" : "images/eye.png";
  });
});
