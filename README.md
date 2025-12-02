# Laravel Starter Kit 🚀

Hey you, this is a production-ready Laravel starter kit that comes pre-configured with JWT authentication, code quality tools, and modern development practices. Skip the boring setup and jump straight into building awesome features! 🚀

## ✨ Features

-   **🔐 JWT Authentication** - Secure token-based authentication out of the box
-   **📊 PHPStan with Larastan** - Static analysis for bulletproof code
-   **🔧 Rector** - Automated code refactoring and modernization
-   **✨ Laravel Pint** - For code formatting
-   **🧪 Testing Setup** - Feature and unit tests included

## 🛠️ Requirements

-   PHP 8.2 or higher

### 📝 Instructions

**Clone the repository**

```bash
git clone git@github.com:lajouiZakariae/laravel-starter-kit.git
cd laravel-starter-kit
```

**Install PHP dependencies**

```bash
composer install
```

**Setup environment configuration**

```bash
cp .env.example .env
```

**Generate application encryption key**

```bash
php artisan key:generate
```

**Generate JWT secret key**

```bash
php artisan jwt:secret
```

**Run database migrations**

```bash
php artisan migrate
```

**Create symbolic link for storage**

```bash
php artisan storage:link
```

**Start the development server**

```bash
php artisan serve

```

## Code Quality Tools 🛠️

This starter kit comes with powerful code quality tools pre-configured to help you maintain clean, modern, and bug-free code.

### 📊 PHPStan with Larastan

**Static analysis tool that finds bugs in your code without running it.**  
Larastan brings Laravel-specific insights on top of PHPStan, giving you smarter checks for models, facades, service containers, and more.

#### Usage

```bash
# Run analysis
./vendor/bin/phpstan

# Run with level 7 (strict)
./vendor/bin/phpstan analyse --level=7

# Generate baseline for existing errors
./vendor/bin/phpstan analyse --generate-baseline
```

### 🔧 Rector

**Automated code refactoring and modernization.**
Rector helps you keep your codebase up-to-date with the latest PHP and Laravel standards by automatically upgrading syntax, applying best practices, and cleaning up legacy code.

#### Usage

```bash
# Run Rector to refactor code
./vendor/bin/rector

# Dry-run (preview changes without applying)
./vendor/bin/rector process --dry-run
```

### ✨ Laravel Pint

**Zero-configuration opinionated code formatter for Laravel.**
Pint ensures your code style is consistent across the project, saving you from endless discussions about formatting in pull requests.

#### Usage

```bash
# Run Pint
./vendor/bin/pint

# Run Pint with verbose output
./vendor/bin/pint -v
```

### Commands to Run

```bash
./vendor/bin/rector --clear-cache && ./vendor/bin/pint
```
