<?php
session_start();
require_once 'config/config.php';
require_once 'includes/auth_check.php';

// Set page title and description
$pageTitle = 'Contact Me';
$pageDescription = 'Get in touch with the Filega search engine developer for questions, feedback, or support';

// Handle form submission
$formSubmitted = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // you would send an email here
        // mail('contact@example.com', $subject, $message, "From: $email");
        
        // For this demo, just mark as submitted
        $formSubmitted = true;
    }
}

// Include header
include_once 'includes/header.php';
?>

<div class="container">
    <div class="contact-header">
        <h1>Contact Us</h1>
        <p class="lead">Have questions or feedback? I'd love to hear from you!</p>
    </div>
    
    <div class="contact-container">
        <div class="contact-info">
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-content">
                    <h3>Email</h3>
                    <p>bamitua1@gmail.com</p>
                    <p class="text-muted">I'll respond within 24 hours</p>
                </div>
            </div>
            
            
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <div class="info-content">
                    <h3>Call Me</h3>
                    <p>+251940926102</p>
                    <p class="text-muted">Mon-Fri, 9AM-6PM</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="info-content">
                    <h3>Live Chat</h3>
                    <p>Available on My website</p>
                    <p class="text-muted">24/7 Support</p>
                </div>
            </div>
            
            <div class="social-links-container">
                <h3>Connect With Me</h3>
                <div class="social-links">
                    <a href="https://www.linkedin.com/in/beamlak-tamirat-801124317/" class="social-link" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="https://x.com/BeamlakTamirat1" class="social-link" aria-label="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    
                </div>
            </div>
        </div>
        
        <div class="contact-form-container">
            <?php if ($formSubmitted): ?>
                <div class="form-success">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2>Thank You!</h2>
                    <p>Your message has been sent successfully. I'll get back to you shortly.</p>
                    <a href="contact.php" class="btn btn-primary">Send Another Message</a>
                </div>
            <?php else: ?>
                <div class="contact-form-header">
                    <h2>Send Me a Message</h2>
                    <p>I'll get back to you as soon as possible</p>
                </div>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form action="contact.php" method="POST" class="contact-form">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-control" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" class="form-control" rows="6" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Send Message</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.contact-header {
    text-align: center;
    margin: 40px 0;
}

.contact-header h1 {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.lead {
    font-size: 1.25rem;
    color: var(--text-secondary);
    max-width: 800px;
    margin: 0 auto;
}

.contact-container {
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    margin-bottom: 60px;
}

.contact-info {
    flex: 1;
    min-width: 300px;
}

.info-item {
    display: flex;
    margin-bottom: 30px;
    align-items: flex-start;
}

.info-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    font-size: 1.25rem;
    margin-right: 20px;
    flex-shrink: 0;
}

.info-content h3 {
    margin-bottom: 5px;
    font-size: 1.25rem;
}

.info-content p {
    margin-bottom: 5px;
}

.text-muted {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.social-links-container {
    margin-top: 40px;
}

.social-links-container h3 {
    margin-bottom: 15px;
    font-size: 1.25rem;
}

.social-links {
    display: flex;
    gap: 15px;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: var(--hover-bg);
    color: var(--text-color);
    border-radius: 50%;
    font-size: 1.125rem;
    transition: all 0.3s ease;
}

.social-link:hover {
    background-color: var(--primary-color);
    color: white;
    transform: translateY(-3px);
    text-decoration: none;
}

.contact-form-container {
    flex: 2;
    min-width: 300px;
    background-color: var(--card-bg);
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px var(--shadow-color);
}

.contact-form-header {
    margin-bottom: 25px;
}

.contact-form-header h2 {
    font-size: 1.75rem;
    margin-bottom: 10px;
}

.contact-form-header p {
    color: var(--text-secondary);
}

.form-success {
    text-align: center;
    padding: 40px 20px;
}

.success-icon {
    font-size: 4rem;
    color: var(--secondary-color);
    margin-bottom: 20px;
}

.form-success h2 {
    font-size: 2rem;
    margin-bottom: 15px;
}

.form-success p {
    margin-bottom: 25px;
    color: var(--text-secondary);
}

@media (max-width: 768px) {
    .contact-container {
        flex-direction: column;
    }
    
    .info-item {
        margin-bottom: 25px;
    }
    
    .contact-form-container {
        padding: 20px;
    }
}
</style>

