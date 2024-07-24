
# Tailwebs

The Teacher Portal is a robust web application designed to streamline the management of student information for teachers. Built with PHP, the portal will provide a secure and user-friendly interface for teachers to log in, view their students, and manage student details efficiently.

## Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL or any other database supported by Laravel

## Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/your-repo-name.git
cd your-repo-name
```

### 2. Install Dependencies

Install PHP dependencies:

```bash
composer install
```

Install Node.js dependencies:

```bash
npm install
```

### 3. Environment Configuration

Copy the example environment configuration file and set up your environment variables:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

### 4. Database Configuration

Open the `.env` file and update the following lines with your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 5. Run Migrations and Seeders

Run the database migrations:

```bash
php artisan migrate
```

Run the database seeders:

```bash
php artisan db:seed
```

### 6. Build Frontend Assets

Compile the frontend assets using Laravel Mix:

```bash
npm run dev
```

For production:

```bash
npm run production
```

### 7. Serve the Application

Start the local development server:

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

Update env APP_URL to  `http://localhost:8000/` Or Your App Url/

## Running Tests

### 1. PHP Unit Tests

Run PHPUnit tests:

```bash
php artisan test
```

### 2. End-to-End Tests (Dusk)

#### Setup Dusk

Make sure ChromeDriver is installed:

```bash
php artisan dusk:chrome-driver --detect
```

Run Dusk tests:

```bash
php artisan dusk
```

Run Migration and Seeder After Tests:
```bash
php artisan migrate --seed
```