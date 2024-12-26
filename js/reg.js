document.getElementById('registrationForm').addEventListener('submit', function(event) {
    let valid = true;

    // Full Name Validation
    const fullName = document.getElementById('fullName');
    if (fullName.value.trim() === '') {
        fullName.classList.add('is-invalid');
        valid = false;
    } else {
        fullName.classList.remove('is-invalid');
    }

    // Email Validation
    const email = document.getElementById('email');
    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,6}$/;
    if (!email.value.match(emailPattern)) {
        email.classList.add('is-invalid');
        valid = false;
    } else {
        email.classList.remove('is-invalid');
    }

    // Password Validation
    const password = document.getElementById('password');
    if (password.value.length < 6) {
        password.classList.add('is-invalid');
        valid = false;
    } else {
        password.classList.remove('is-invalid');
    }

    // Confirm Password Validation
    const confirmPassword = document.getElementById('confirmPassword');
    if (password.value !== confirmPassword.value) {
        confirmPassword.classList.add('is-invalid');
        valid = false;
    } else {
        confirmPassword.classList.remove('is-invalid');
    }

    if (!valid) {
        event.preventDefault();
    }
});

// Show Password Toggle
const togglePassword = document.querySelectorAll('.toggle-password');
togglePassword.forEach(function(element) {
    element.addEventListener('click', function() {
        const input = document.querySelector(this.getAttribute('toggle'));
        if (input.type === 'password') {
            input.type = 'text';
            this.innerHTML = '<i class="bi bi-eye"></i>';
        } else {
            input.type = 'password';
            this.innerHTML = '<i class="bi bi-eye-slash"></i>';
        }
    });
});
