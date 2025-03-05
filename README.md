# Laravel Installation Guide

This guide provides step-by-step instructions on setting up a Laravel project after cloning the repository.

## Prerequisites
Ensure that the following dependencies are installed on your system:
- **PHP** (Minimum version 8.2 or as required by the project)
- **Composer**
- **Database** (MySQL)

## Installation Steps

### 1. Clone the Repository
Clone the project from GitHub or GitLab:
```bash
git clone https://gitlab.com/andruchristo27/ecommerce.git
cd repository
```

### 2. Install Dependencies
Run the following command to install PHP dependencies:
```bash
composer install
```

### 3. Configure Environment Variables
Copy the example `.env` file and configure it:
```bash
cp .env.example .env
```
Edit the `.env` file to match your database settings:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 4. Generate Application Key
Generate the application encryption key:
```bash
php artisan key:generate
```

### 5. Run Migrations and Seeders (Optional)
Run database migrations:
```bash
php artisan migrate
```
If there are seeders available, run:
```bash
php artisan migrate --seed
```

### 6. Serve the Application
Run the Laravel development server:
```bash
php artisan serve
```
Access the application in the browser:
```
http://localhost:8000
```

This completes the Laravel setup process. Happy coding!