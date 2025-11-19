document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("password");
  const toggleIcon = document.getElementById("togglePassword");

  toggleIcon.addEventListener("click", function () {
    const isPassword = passwordInput.type === "password";
    passwordInput.type = isPassword ? "text" : "password";
    toggleIcon.src = isPassword ? "images/eye-slash.png" : "images/eye.png";
  });
});
