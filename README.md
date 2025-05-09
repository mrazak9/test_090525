# INABA Coding Test - CRUD Application

This project was developed as a solution for the INABA coding test. It implements a CRUD application for managing products and order processing using Laravel 10 and PHP 8.1.

## Features

- Complete CRUD operations for all tables
- Automated stock reduction upon product checkout
- Validation to prevent checkout when quantity exceeds available stock
- User-friendly interface for all forms

## System Requirements

- PHP 8.1 or higher
- Composer
- MySQL/MariaDB
- Git

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/YOUR_USERNAME/YOUR_REPOSITORY.git
cd YOUR_REPOSITORY
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Create Database

Create a new MySQL database for the application:

```sql
CREATE DATABASE database_name;
```

### 4. Create and Configure Environment File

```bash
cp .env.example .env
```

Now edit the `.env` file and update the database configuration:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database_name
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations and Seeders

```bash
php artisan migrate
php artisan db:seed
```

### 7. Start the Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Database Structure

The application uses the following table structure:

- users: Stores user information
- products: Contains product details including stock quantities
- orders: Records order information
- order_details: Contains items within each order

## Usage

1. Register an account or login
2. Browse available products
3. Add products to cart (quantity validation applied automatically)
4. Proceed to checkout
5. View order history

## Admin Access

To access admin features:
- Login with admin credentials
- Navigate to the admin dashboard
- Manage products, orders, and users

for example access:
- email address: admin@admin.com
- password: password123

## Technologies Used

- Laravel 10
- PHP 8.1
- MySQL
- Bootstrap/Tailwind CSS (choose what you used)
- JavaScript/Vue.js (if applicable)

## Notes

This application was developed as part of a coding test for INABA. It demonstrates CRUD functionality, stock management, and form validation as per the requirements.