<?php
session_start();
require_once 'config/config.php';
require_once 'includes/auth_check.php';

// Set page title and description
$pageTitle = 'About';
$pageDescription = 'Learn more about Filega - An advanced search engine with personalized features';

// Include header
include_once 'includes/header.php';
?>

<div class="container">
    <div class="about-header">
        <h1>About Filega</h1>
        <p class="lead">An advanced search engine built for better results and personalized experience</p>
    </div>
    
    <div class="about-content">
        <section class="about-section">
            <h2>Mission</h2>
            <p>
                At Filega, my mission is to provide an intuitive and efficient search experience that helps users find exactly what they're looking for. 
                I believe in the power of personalization and strive to deliver search results that are tailored to each user's needs.
            </p>
        </section>
        
        <section class="about-section">
            <h2>Key Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Powerful Search</h3>
                    <p>
                        Utilizing Google's advanced search technology to deliver accurate and relevant results for any query.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3>Search History</h3>
                    <p>
                        Keep track of your searches with our comprehensive history feature, making it easy to revisit previous queries.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bookmark"></i>
                    </div>
                    <h3>Bookmarks</h3>
                    <p>
                        Save important search results for later reference with our convenient bookmark system.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3>Secure Account</h3>
                    <p>
                        Create a personal account to save your preferences, history, and bookmarks securely.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-moon"></i>
                    </div>
                    <h3>Dark Mode</h3>
                    <p>
                        Enjoy a comfortable search experience day or night with our customizable interface themes.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <h3>Fast & Responsive</h3>
                    <p>
                        Designed for speed and efficiency across all devices, from desktop to mobile.
                    </p>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <h2>How Filega Works</h2>
            <div class="how-it-works">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Create an Account</h3>
                        <p>Sign up with your email to access personalized features and save your preferences.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Search for Anything</h3>
                        <p>Use our powerful search engine to find information, images, videos, and more across the web.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Save & Organize</h3>
                        <p>Bookmark important results and access your search history to keep track of your research.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Personalize Your Experience</h3>
                        <p>Adjust settings to customize your search experience according to your preferences.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="about-section">
            <h2>About the Developer</h2>
            <p>
                Filega is created by Beamlak Tamirat, a full stack developer. This project was primarily built using PHP, 
                along with other web technologies to deliver a fast, responsive, and user-friendly search experience.
            </p>
        </section>
        
    </div>
</div>

<style>
.about-header {
    text-align: center;
    margin: 40px 0 60px;
}

.about-header h1 {
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

.about-section {
    margin-bottom: 60px;
}

.about-section h2 {
    font-size: 2rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--border-color);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.feature-card {
    background-color: var(--card-bg);
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px var(--shadow-color);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px var(--shadow-color);
}

.feature-icon {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.feature-card h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.how-it-works {
    margin-top: 30px;
}

.step {
    display: flex;
    margin-bottom: 30px;
    align-items: flex-start;
}

.step-number {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    font-size: 1.5rem;
    font-weight: 700;
    margin-right: 20px;
    flex-shrink: 0;
}

.step-content h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.tech-stack {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 30px;
}

.tech-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: var(--card-bg);
    padding: 20px;
    border-radius: 8px;
    width: 120px;
    box-shadow: 0 2px 5px var(--shadow-color);
    transition: transform 0.2s ease;
}

.tech-item:hover {
    transform: translateY(-5px);
}

.tech-item i {
    font-size: 2.5rem;
    margin-bottom: 10px;
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .features-grid {
        grid-template-columns: 1fr;
    }
    
    .step {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .step-number {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .tech-stack {
        justify-content: center;
    }
}
</style>

<?php include_once 'includes/footer.php'; ?> 