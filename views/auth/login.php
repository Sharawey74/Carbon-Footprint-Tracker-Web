<div class="login-page">
    <div class="login-container">
        <div class="login-card">
            <h1 class="login-heading">Login</h1>
            
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
                    <?php unset($_SESSION['error']) ?>
                <?php endif; ?>
            
            <form method="POST" action="<?= APP_URL ?>/?controller=auth&action=login" id="loginForm">
                <div class="form-group">
                    <label for="email" class="form-label">User</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="username@example.com" required>
                    </div>
                
                <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                    <div class="password-container">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="login-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember</label>
                    </div>
                    <div class="forgot-password">
                        <a href="<?= APP_URL ?>/?controller=auth&action=forgotPassword">Forget Password</a>
                    </div>
                </div>
                
                <div class="login-button">
                    <button type="submit" class="btn-login">Login</button>
                    </div>
                </form>
        </div>
    </div>
</div>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* Login Page Specific Styles */
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #00FF7F, #009688); /* Spring Green to Teal gradient */
    background-size: 400% 400%;
    animation: gradientAnimation 15s ease infinite;
    padding: 20px;
    position: relative;
}

/* Animated gradient background */
@keyframes gradientAnimation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Grain texture overlay - keeping it monochrome */
.login-page::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.08'/%3E%3C/svg%3E");
    pointer-events: none;
    opacity: 0.3;
    mix-blend-mode: overlay;
}

.login-container {
    width: 100%;
    max-width: 400px;
    animation: fadeIn 0.5s ease-in-out;
    position: relative;
    z-index: 1;
}

.login-card {
    background: rgba(0, 255, 127, 0.15); /* Spring Green overlay */
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 16px;
    border: 1px solid rgb(239, 247, 246); /* Teal border */
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    padding: 32px;
    transition: all 0.3s ease;
}

.login-card:hover {
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    transform: translateY(-5px);
}

.login-heading {
    color:rgba(255, 255, 255, 0.88); /* Teal for text */
    font-size: 24px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 24px;
    text-shadow: none;
}

.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    color: #009688; /* Teal */
    font-size: 14px;
    font-weight: 600;
    text-shadow: none;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    background: rgba(0, 255, 127, 0.1); /* Spring Green background */
    border: 1px solid rgba(0, 150, 136, 0.3); /* Teal border */
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    color: #006064; /* Dark teal for text */
}

.form-control:focus {
    background: rgba(0, 255, 127, 0.2);
    border-color: rgba(0, 150, 136, 0.6);
    box-shadow: 0 0 0 3px rgba(0, 150, 136, 0.2);
}

.form-control::placeholder {
    color: rgba(0, 150, 136, 0.4);
}

.password-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #009688; /* Teal */
    cursor: pointer;
    padding: 0;
}

.login-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    font-size: 14px;
    color: #009688; /* Teal */
}

.remember-me {
    display: flex;
    align-items: center;
}

.remember-me input[type="checkbox"] {
    margin-right: 8px;
}

.forgot-password a {
    color: #009688; /* Teal */
    text-decoration: none;
    transition: color 0.3s ease;
}

.forgot-password a:hover {
    color: #00796B; /* Darker teal on hover */
    text-decoration: underline;
}

.btn-login {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #00FF7F, #009688); /* Spring Green to Teal gradient */
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,150,136,0.3);
}

.btn-login:hover {
    background: linear-gradient(135deg, #00E676, #00897B); /* Slightly darker gradient on hover */
    box-shadow: 0 6px 16px rgba(0,150,136,0.4);
    transform: translateY(-2px);
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Form validation */
.form-control.error {
    border-color: #ff5252; /* Keep red for errors if needed */
    background: rgba(255, 82, 82, 0.1);
}

.error-message {
    color: #b71c1c;
    font-size: 12px;
    margin-top: 4px;
    text-shadow: none;
}

/* Alert styling */
.alert {
    background: rgba(0, 255, 127, 0.1);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 8px;
    border-left: 4px solid;
    margin-bottom: 20px;
}

.alert-danger {
    border-color: #009688;
    color: #00796B;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            if (type === 'password') {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        });
        
        // Form validation
        const loginForm = document.getElementById('loginForm');
        const emailInput = document.getElementById('email');
        
        loginForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Email validation
            if (!emailInput.value.trim()) {
                emailInput.classList.add('error');
                isValid = false;
                
                // Add error message if not exists
                if (!document.getElementById('email-error')) {
                    const errorMsg = document.createElement('div');
                    errorMsg.id = 'email-error';
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'Email is required';
                    emailInput.parentNode.appendChild(errorMsg);
                }
            } else {
                emailInput.classList.remove('error');
                const errorMsg = document.getElementById('email-error');
                if (errorMsg) errorMsg.remove();
            }
            
            // Password validation
            if (!passwordInput.value.trim()) {
                passwordInput.classList.add('error');
                isValid = false;
                
                // Add error message if not exists
                if (!document.getElementById('password-error')) {
                    const errorMsg = document.createElement('div');
                    errorMsg.id = 'password-error';
                    errorMsg.className = 'error-message';
                    errorMsg.textContent = 'Password is required';
                    passwordInput.parentNode.appendChild(errorMsg);
                }
            } else {
                passwordInput.classList.remove('error');
                const errorMsg = document.getElementById('password-error');
                if (errorMsg) errorMsg.remove();
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>