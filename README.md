# Filega Search Engine

Filega is a modern, personalized search engine built with PHP, MySQL, HTML, CSS, and JavaScript. It integrates with the Google Custom Search API to provide powerful search functionality while adding features like user accounts, search history, bookmarks, and more.

## Features

- **Powerful Search**: Integration with Google Custom Search API for accurate results
- **User Accounts**: Register, login, and password recovery functionality
- **Search History**: Automatic tracking of search queries for logged in users
- **Bookmarks**: Save important search results for later reference
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Dark Mode**: Toggle between light and dark themes
- **Modern Interface**: Clean and intuitive UI for better user experience

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Google Custom Search API key and Search Engine ID

## Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/yourusername/filega.git
   cd filega
   ```

2. **Configure the application**

   Edit `config/config.php` and add your database credentials and Google API keys:

   ```php
   // Database Configuration
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_database_user');
   define('DB_PASS', 'your_database_password');
   define('DB_NAME', 'filega_db');

   // Google Custom Search API Configuration
   define('GOOGLE_API_KEY', 'your_google_api_key'); 
   define('GOOGLE_SEARCH_ENGINE_ID', 'your_search_engine_id');
   ```

3. **Set up the database**

   Visit `setup/db_setup.php` in your browser to automatically create the database and tables.

4. **Set up a virtual host (optional)**

   For the best experience, configure a virtual host pointing to the project directory.

## Getting Google API Keys

1. Go to the [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project
3. Enable the "Custom Search API"
4. Create API credentials to get your API key
5. Go to the [Custom Search Engine](https://cse.google.com/cse/all) page
6. Create a new search engine and get your Search Engine ID

## Project Structure

- `/assets`: Contains CSS, JavaScript, and image files
- `/auth`: Authentication related files (login, register, etc.)
- `/api`: AJAX API endpoints
- `/config`: Application configuration
- `/includes`: Reusable components and functions
- `/setup`: Database setup script

## Default Login

After running the database setup, a default admin user is created:

- **Email**: admin@example.com
- **Password**: password123

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Credits

Filega Search Engine was created as a demonstration of integrating Google Custom Search API with a modern web application.

## Screenshots

![Home Page](screenshots/home.png)
![Search Results](screenshots/search.png)
![Search History](screenshots/history.png) 