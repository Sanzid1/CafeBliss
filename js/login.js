document.getElementById('loginForm').addEventListener('submit', function(event) {
    let valid = true;

    // Email Validation
    const email = document.getElementById('loginEmail');
    if (email.value.trim() === '') {
        email.classList.add('is-invalid');
        valid = false;
    } else {
        email.classList.remove('is-invalid');
    }

    // Password Validation
    const password = document.getElementById('loginPassword');
    if (password.value.trim() === '') {
        password.classList.add('is-invalid');
        valid = false;
    } else {
        password.classList.remove('is-invalid');
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
