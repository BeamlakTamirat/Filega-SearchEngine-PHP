<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME; ?></title>
    <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Filega - Advanced Search Engine'; ?>">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo baseUrl(); ?>/assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo baseUrl(); ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Theme detection -->
    <script>
        // Check for saved theme preference or get from OS preference
        const getTheme = () => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                return savedTheme;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };
        
        // Apply theme
        const setTheme = (theme) => {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
        };
        
        // Set theme on page load
        setTheme(getTheme());
    </script>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <nav class="navbar">
                <div class="navbar-logo">
                    <a href="<?php echo baseUrl(); ?>/">
                        <img src="<?php echo baseUrl(); ?>/assets/img/filega-logo-small.png" style="margin-top:15px;" alt="<?php echo APP_NAME; ?>" class="logo-small">
                    </a>
                </div>
                
                <div class="navbar-menu">
                    <ul class="nav-links">
                        <li><a href="<?php echo baseUrl(); ?>/" class="<?php echo $_SERVER['PHP_SELF'] === '/index.php' ? 'active' : ''; ?>">Home</a></li>
                        <li><a href="<?php echo baseUrl(); ?>/about.php" class="<?php echo $_SERVER['PHP_SELF'] === '/about.php' ? 'active' : ''; ?>">About</a></li>
                        <li><a href="<?php echo baseUrl(); ?>/contact.php" class="<?php echo $_SERVER['PHP_SELF'] === '/contact.php' ? 'active' : ''; ?>">Contact</a></li>
                    </ul>
                </div>
                
                <div class="navbar-actions">
                    <div class="theme-toggle">
                        <button id="theme-toggle-btn" aria-label="Toggle theme">
                            <i class="fas fa-moon"></i>
                            <i class="fas fa-sun"></i>
                        </button>
                    </div>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="user-dropdown">
                            <button class="dropdown-toggle">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo getCurrentUserName(); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="<?php echo baseUrl(); ?>/profile.php">
                                    <i class="fas fa-user"></i> Profile
                                </a>
                                <a href="<?php echo baseUrl(); ?>/history.php">
                                    <i class="fas fa-history"></i> Search History
                                </a>
                                <a href="<?php echo baseUrl(); ?>/bookmarks.php">
                                    <i class="fas fa-bookmark"></i> Bookmarks
                                </a>
                                <a href="<?php echo baseUrl(); ?>/settings.php">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="<?php echo baseUrl(); ?>/auth/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="<?php echo baseUrl(); ?>/auth/login.php" class="btn btn-outline">Login</a>
                            <a href="<?php echo baseUrl(); ?>/auth/register.php" class="btn btn-primary">Register</a>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    
    <main class="main-content"> 