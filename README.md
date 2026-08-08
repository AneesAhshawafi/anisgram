<div align="center">

# 🚀 Anisgram

A scalable, containerized Laravel web application built with modern architecture standards, robust testing, and full Docker orchestration.

[![CI Status](https://github.com/AneesAhshawafi/anisgram/actions/workflows/ci.yml/badge.svg)](https://github.com/AneesAhshawafi/anisgram/actions)
[![Laravel Version](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.5-777BB4?style=flat&logo=php)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Sail-2496ED?style=flat&logo=docker)](https://laravel.com/docs/sail)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

</div>

---

## 🏗 Architecture & Tech Stack

This application follows clean architecture principles to ensure maintainability, type safety, and seamless containerization.

| Component | Technology / Tool | Description |
| :--- | :--- | :--- |
| **Framework** | [Laravel 13.x](https://laravel.com) | Core backend web framework |
| **Environment** | PHP 8.5 via Docker ([Laravel Sail](https://laravel.com/docs/sail)) | Containerized development runtime |
| **Database** | MySQL 8.4 | Primary relational storage |
| **Cache & Queue** | Redis (Alpine) | In-memory data store & queue handler |
| **Code Quality** | [Laravel Pint](https://laravel.com/docs/pint) & [Larastan](https://github.com/larastan/larastan) | Code styling & static analysis |
| **Testing** | [Pest PHP](https://pestphp.com) / PHPUnit | Test-driven development suite |

---

## 🛠 Prerequisites

Ensure you have the following installed on your local environment:

* 🐳 **Docker Desktop** (or Docker Engine with WSL 2 on Windows)
* 🐙 **Git**

---

## ⚡ Quick Start & Installation

Follow these steps to spin up the development environment in less than 2 minutes:

### 1. Clone the Repository
```bash
git clone https://github.com/AneesAhshawafi/anisgram.git.git
cd repository-name
```

### 2. Configure Environment Variables
Copy the sanitized environment template:
```bash
cp .env.example .env
```

### 3. Start Application Containers
Run Sail in detached mode:
```bash
./vendor/bin/sail up -d
```

> [!TIP]
> Optionally create a shell alias for Sail for easier command execution:
> ```bash
> alias sail='[ -f sail ] && sh sail || ./vendor/bin/sail'
> ```

### 4. Initialize Application
Generate the application key and execute database migrations with seeders:
```bash
sail artisan key:generate
sail artisan migrate --seed
```

### 5. Access Application
Open your browser and navigate to:
```text
http://localhost
```

---

## 🧪 Testing & Code Quality

Maintain code quality and ensure test suites pass before submitting pull requests:

```bash
# 🧪 Run automated tests
sail artisan test

# 🎨 Format code according to Laravel standards
sail bin pint

# 🔍 Run static analysis
sail bin phpstan analyse
```

---

## 📁 Key Directory Structure

```text
.
├── app/
│   ├── Actions/          # Single-responsibility business logic classes
│   ├── Http/             # Controllers, Middleware, and Form Requests
│   ├── Models/           # Eloquent Models
│   └── Services/         # Service layer abstractions
├── database/
│   ├── factories/        # Model factories for testing
│   ├── migrations/       # Schema definitions
│   └── seeders/          # Database seeders
├── tests/                # Feature and Unit tests
└── docker-compose.yml    # Docker Sail container orchestration
```

---

## 📄 License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
