/**
 * Second Chance - University Marketplace
 * Client-Side Validations and Interactive Behaviors
 */

document.addEventListener("DOMContentLoaded", function () {
    // -------------------------------------------------------------
    // 1. Password Visibility Toggle
    // -------------------------------------------------------------
    const toggleButtons = document.querySelectorAll(".password-toggle-btn");
    toggleButtons.forEach(button => {
        button.addEventListener("click", function () {
            const targetId = this.getAttribute("data-target");
            const input = document.getElementById(targetId);
            if (input) {
                if (input.type === "password") {
                    input.type = "text";
                    this.textContent = "Hide";
                } else {
                    input.type = "password";
                    this.textContent = "Show";
                }
            }
        });
    });

    // -------------------------------------------------------------
    // 2. Signup Form Client-Side Validations
    // -------------------------------------------------------------
    const signupForm = document.getElementById("signupForm");
    if (signupForm) {
        const fnameInput = document.getElementById("fname");
        const lnameInput = document.getElementById("lname");
        const emailInput = document.getElementById("email");
        const phoneInput = document.getElementById("phone");
        const passwordInput = document.getElementById("password");
        const confirmPasswordInput = document.getElementById("confirm_password");

        // Checklist elements for password
        const checkLen = document.getElementById("check-len");
        const checkLetter = document.getElementById("check-letter");
        const checkNumber = document.getElementById("check-number");
        const checkMatch = document.getElementById("check-match");

        // Helper: Show/Clear Field Error
        function setFieldError(input, errorElementId, message) {
            if (!input) return;
            input.classList.remove("is-valid");
            input.classList.add("is-invalid");
            const errEl = document.getElementById(errorElementId);
            if (errEl) {
                errEl.textContent = message;
                errEl.style.display = "block";
            }
        }

        function setFieldValid(input, errorElementId) {
            if (!input) return;
            input.classList.remove("is-invalid");
            input.classList.add("is-valid");
            const errEl = document.getElementById(errorElementId);
            if (errEl) {
                errEl.textContent = "";
                errEl.style.display = "none";
            }
        }

        // Live First Name Validation
        function validateFname() {
            if (!fnameInput) return true;
            const val = fnameInput.value.trim();
            if (val === "") {
                setFieldError(fnameInput, "fname-error", "First name is required.");
                return false;
            } else if (val.length < 2) {
                setFieldError(fnameInput, "fname-error", "First name must be at least 2 characters.");
                return false;
            } else if (!/^[a-zA-Z\s'-]+$/.test(val)) {
                setFieldError(fnameInput, "fname-error", "First name can only contain letters.");
                return false;
            }
            setFieldValid(fnameInput, "fname-error");
            return true;
        }

        // Live Last Name Validation
        function validateLname() {
            if (!lnameInput) return true;
            const val = lnameInput.value.trim();
            if (val === "") {
                setFieldError(lnameInput, "lname-error", "Last name is required.");
                return false;
            } else if (val.length < 2) {
                setFieldError(lnameInput, "lname-error", "Last name must be at least 2 characters.");
                return false;
            } else if (!/^[a-zA-Z\s'-]+$/.test(val)) {
                setFieldError(lnameInput, "lname-error", "Last name can only contain letters.");
                return false;
            }
            setFieldValid(lnameInput, "lname-error");
            return true;
        }

        // Live Email Validation
        function validateEmail() {
            if (!emailInput) return true;
            const val = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (val === "") {
                setFieldError(emailInput, "email-error", "Email address is required.");
                return false;
            } else if (!emailRegex.test(val)) {
                setFieldError(emailInput, "email-error", "Please enter a valid university or personal email address.");
                return false;
            }
            setFieldValid(emailInput, "email-error");
            return true;
        }

        // Live Phone Validation (Supports Egyptian numbers & standard 10-20 digits)
        function validatePhone() {
            if (!phoneInput) return true;
            const val = phoneInput.value.trim();
            const cleanDigits = val.replace(/\s+/g, '');
            const generalPhoneRegex = /^[0-9]{10,20}$/;

            if (val === "") {
                setFieldError(phoneInput, "phone-error", "Phone number is required.");
                return false;
            } else if (!generalPhoneRegex.test(cleanDigits)) {
                setFieldError(phoneInput, "phone-error", "Please enter a valid 10-20 digit phone number (e.g. 01012345678).");
                return false;
            }
            setFieldValid(phoneInput, "phone-error");
            return true;
        }

        // Live Password Checklist & Validation
        function validatePassword() {
            if (!passwordInput) return true;
            const val = passwordInput.value;
            const hasLen = val.length >= 8;
            const hasLetter = /[a-zA-Z]/.test(val);
            const hasNumber = /[0-9]/.test(val);

            if (checkLen) {
                checkLen.className = hasLen ? "valid" : "invalid";
                checkLen.innerHTML = (hasLen ? "✓" : "•") + " At least 8 characters long";
            }
            if (checkLetter) {
                checkLetter.className = hasLetter ? "valid" : "invalid";
                checkLetter.innerHTML = (hasLetter ? "✓" : "•") + " Contains at least one letter";
            }
            if (checkNumber) {
                checkNumber.className = hasNumber ? "valid" : "invalid";
                checkNumber.innerHTML = (hasNumber ? "✓" : "•") + " Contains at least one number";
            }

            if (val === "") {
                setFieldError(passwordInput, "password-error", "Password is required.");
                return false;
            } else if (!hasLen || !hasLetter || !hasNumber) {
                setFieldError(passwordInput, "password-error", "Password must meet all checklist requirements.");
                return false;
            }
            setFieldValid(passwordInput, "password-error");
            validateConfirmPassword();
            return true;
        }

        // Live Confirm Password Match
        function validateConfirmPassword() {
            if (!confirmPasswordInput || !passwordInput) return true;
            const pVal = passwordInput.value;
            const cVal = confirmPasswordInput.value;
            const isMatch = pVal.length > 0 && pVal === cVal;

            if (checkMatch) {
                checkMatch.className = isMatch ? "valid" : "invalid";
                checkMatch.innerHTML = (isMatch ? "✓" : "•") + " Passwords match";
            }

            if (cVal === "") {
                setFieldError(confirmPasswordInput, "confirm-password-error", "Please confirm your password.");
                return false;
            } else if (pVal !== cVal) {
                setFieldError(confirmPasswordInput, "confirm-password-error", "Passwords do not match.");
                return false;
            }
            setFieldValid(confirmPasswordInput, "confirm-password-error");
            return true;
        }

        // Event listeners
        if (fnameInput) fnameInput.addEventListener("input", validateFname);
        if (lnameInput) lnameInput.addEventListener("input", validateLname);
        if (emailInput) emailInput.addEventListener("input", validateEmail);
        if (phoneInput) phoneInput.addEventListener("input", validatePhone);
        if (passwordInput) passwordInput.addEventListener("input", validatePassword);
        if (confirmPasswordInput) confirmPasswordInput.addEventListener("input", validateConfirmPassword);

        // Form Submit Handler
        signupForm.addEventListener("submit", function (e) {
            const isFnValid = validateFname();
            const isLnValid = validateLname();
            const isEValid = validateEmail();
            const isPValid = validatePhone();
            const isPwValid = validatePassword();
            const isCpValid = validateConfirmPassword();

            if (!isFnValid || !isLnValid || !isEValid || !isPValid || !isPwValid || !isCpValid) {
                e.preventDefault();
                const firstInvalid = signupForm.querySelector(".is-invalid");
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    }


    // -------------------------------------------------------------
    // 3. Login Form Client-Side Validations
    // -------------------------------------------------------------
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        const emailInput = document.getElementById("loginEmail");
        const passwordInput = document.getElementById("loginPassword");

        loginForm.addEventListener("submit", function (e) {
            let isValid = true;
            const emailVal = emailInput.value.trim();
            const passVal = passwordInput.value;

            if (emailVal === "") {
                emailInput.classList.add("is-invalid");
                isValid = false;
            } else {
                emailInput.classList.remove("is-invalid");
            }

            if (passVal === "") {
                passwordInput.classList.add("is-invalid");
                isValid = false;
            } else {
                passwordInput.classList.remove("is-invalid");
            }

            if (!isValid) {
                e.preventDefault();
                const errBox = document.getElementById("login-client-error");
                if (errBox) {
                    errBox.textContent = "Please fill in all required fields.";
                    errBox.style.display = "block";
                }
            }
        });
    }
});
