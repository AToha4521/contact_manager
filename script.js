// script.js

// Simulate a pop-up for demonstration
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('#loginCard form');
    const popup = document.getElementById('popup');

    // Show pop-up on form submit
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();
        popup.classList.add('show');

        // Hide pop-up after 2 seconds
        setTimeout(() => {
            popup.classList.remove('show');
        }, 2000);

        // Optional: Redirect to home page after showing the message
        setTimeout(() => {
            window.location.href = "home.php";
        }, 2200);
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('#loginCard form');
    const signupForm = document.querySelector('#signupCard form');
    const popup = document.getElementById('popup');

    function showPopup(message, success = true) {
        popup.textContent = message;
        popup.style.backgroundColor = success ? '#4caf50' : '#f44336';
        popup.classList.add('show');
        
        // Hide the pop-up after 2 seconds
        setTimeout(() => {
            popup.classList.remove('show');
        }, 2000);
    }

    // Inline validation
    function validateForm(form) {
        let valid = true;
        form.querySelectorAll('input').forEach(input => {
            if (input.value.trim() === '') {
                valid = false;
                input.classList.add('error');
                showPopup('Please fill out all fields', false);
            } else {
                input.classList.remove('error');
            }
        });
        return valid;
    }

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();
        if (validateForm(loginForm)) {
            showPopup("Welcome back!");
            // Optional: Redirect to home page after showing the message
            setTimeout(() => {
                window.location.href = "home.php";
            }, 2000);
        }
    });

    signupForm.addEventListener('submit', function(event) {
        event.preventDefault();
        if (validateForm(signupForm)) {
            showPopup("Account created successfully!");
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.form-container');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    form.addEventListener('submit', (event) => {
        if (!usernameInput.value.trim()) {
            alert('Username cannot be empty!');
            event.preventDefault();
            return;
        }

        if (passwordInput.value.length < 6) {
            alert('Password must be at least 6 characters long!');
            event.preventDefault();
        }
    });
});
