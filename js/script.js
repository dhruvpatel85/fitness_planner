// Main JavaScript file for Fitness Planner

document.addEventListener('DOMContentLoaded', function() {
    // Mobile navigation toggle
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.navbar')) {
            navMenu.classList.remove('active');
        }
    });

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            updatePasswordStrengthIndicator(strength);
        });
    }

    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!validateForm(this)) {
                event.preventDefault();
            }
        });
    });

    // BMI Calculator (if on profile page)
    if (window.location.pathname.includes('profile.php')) {
        setupBMICalculator();
    }
});

function checkPasswordStrength(password) {
    let strength = 0;

    // Length check
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;

    // Contains numbers
    if (/\d/.test(password)) strength++;

    // Contains lowercase and uppercase
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;

    // Contains special characters
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    return strength;
}

function updatePasswordStrengthIndicator(strength) {
    const indicator = document.getElementById('password-strength');
    if (!indicator) return;

    const colors = ['#dc3545', '#ff6b6b', '#ffc107', '#28a745', '#20c997'];
    const messages = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];

    indicator.style.width = (strength * 20) + '%';
    indicator.style.backgroundColor = colors[strength];
    indicator.textContent = messages[strength];
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

    inputs.forEach(input => {
        if (!input.value.trim()) {
            showError(input, 'This field is required');
            isValid = false;
        } else {
            clearError(input);
        }

        // Email validation
        if (input.type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                showError(input, 'Please enter a valid email address');
                isValid = false;
            }
        }

        // Password confirmation
        if (input.name === 'confirm_password') {
            const password = form.querySelector('input[name="password"]');
            if (password && input.value !== password.value) {
                showError(input, 'Passwords do not match');
                isValid = false;
            }
        }
    });

    return isValid;
}

function showError(input, message) {
    clearError(input);

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = 'var(--danger)';
    errorDiv.style.fontSize = '0.9rem';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;

    input.style.borderColor = 'var(--danger)';
    input.parentNode.appendChild(errorDiv);
}

function clearError(input) {
    const errorDiv = input.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
    input.style.borderColor = '';
}

function setupBMICalculator() {
    const heightInput = document.getElementById('height');
    const weightInput = document.getElementById('current_weight');

    if (heightInput && weightInput) {
        function calculateBMI() {
            const height = parseFloat(heightInput.value) / 100; // Convert cm to m
            const weight = parseFloat(weightInput.value);

            if (height && weight) {
                const bmi = weight / (height * height);
                const bmiDisplay = document.getElementById('bmi-result');

                if (bmiDisplay) {
                    bmiDisplay.textContent = bmi.toFixed(1);

                    // Update BMI category
                    let category = '';
                    let color = '';

                    if (bmi < 18.5) {
                        category = 'Underweight';
                        color = '#ffc107';
                    } else if (bmi < 25) {
                        category = 'Normal';
                        color = '#28a745';
                    } else if (bmi < 30) {
                        category = 'Overweight';
                        color = '#fd7e14';
                    } else {
                        category = 'Obese';
                        color = '#dc3545';
                    }

                    document.getElementById('bmi-category').textContent = category;
                    document.getElementById('bmi-category').style.color = color;
                }
            }
        }

        heightInput.addEventListener('input', calculateBMI);
        weightInput.addEventListener('input', calculateBMI);
    }
}

// AJAX helper function
function ajaxRequest(url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            } else {
                callback({ success: false, error: 'Request failed' });
            }
        }
    };

    // Convert data object to URL-encoded string
    const encodedData = Object.keys(data).map(key => {
        return encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
    }).join('&');

    xhr.send(encodedData);
}

// Toast notification system
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;

    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.padding = '15px 25px';
    toast.style.background = type === 'success' ? 'var(--success)' : 'var(--danger)';
    toast.style.color = 'white';
    toast.style.borderRadius = '8px';
    toast.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
    toast.style.zIndex = '9999';
    toast.style.transform = 'translateX(150%)';
    toast.style.transition = 'transform 0.3s ease';

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 10);

    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(150%)';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
// Fix duplicate navigation menus
function fixDuplicateNavs() {
    // Get all nav elements
    const navs = document.querySelectorAll('nav.navbar');

    // If we have more than one navbar
    if (navs.length > 1) {
        console.log('Found', navs.length, 'navigation menus. Keeping only the first one.');

        // Hide all but the first navbar
        for (let i = 1; i < navs.length; i++) {
            navs[i].style.display = 'none';
        }

        // Also check for navs inside container
        const container = document.querySelector('.container');
        if (container) {
            const containerNavs = container.querySelectorAll('nav.navbar');
            containerNavs.forEach(nav => {
                nav.style.display = 'none';
            });
        }
    }

    // Also check for duplicate navs in body (not as first child)
    const bodyChildren = document.body.children;
    let firstNavFound = false;

    for (let child of bodyChildren) {
        if (child.tagName === 'NAV' && child.classList.contains('navbar')) {
            if (!firstNavFound) {
                firstNavFound = true;
            } else {
                child.style.display = 'none';
            }
        }
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    fixDuplicateNavs();

    // Also run after a short delay to catch dynamically added elements
    setTimeout(fixDuplicateNavs, 100);
});