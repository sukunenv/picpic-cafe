# 🍵 Picpic Cafe — Backend API

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Sanctum](https://img.shields.io/badge/Sanctum-Auth-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

> 🔥 Production-ready REST API for Picpic Cafe — built with Laravel 12, the latest and most powerful version of the framework.
> Dari autentikasi hingga analytics — semua tersedia dalam satu API yang cepat, aman, dan scalable.

## ✨ Features
- 🔐 Authentication via Laravel Sanctum
- 📋 Menu & Category Management
- 🛒 Order & Cart System  
- 📊 Analytics & Reporting
- 🖼️ Banner Management
- 🔄 RESTful API with proper HTTP methods

## 🛠️ Tech Stack
- **Framework:** Laravel 12 (latest)
- **Language:** PHP 8.x
- **Database:** MySQL 8
- **Auth:** Laravel Sanctum
- **Deployment:** Railway

## 📡 API Endpoints

### Public
- `GET /api/v1/categories`
- `GET /api/v1/menus`
- `GET /api/v1/menus/{slug}`
- `GET /api/v1/banners`
- `POST /api/v1/register`
- `POST /api/v1/login`

### Protected (Bearer Token)
- `POST /api/v1/logout`
- `GET /api/v1/profile`
- `GET /api/v1/orders`
- `POST /api/v1/orders`
- `GET /api/v1/cart`
- `POST /api/v1/cart`

## 🚀 Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 8

### Installation
1. Clone the repository
   ```bash
   git clone https://github.com/sukunenv/picpic-cafe.git
   cd picpic-cafe
   ```
2. Install dependencies
   ```bash
   composer install
   ```
3. Environment Setup
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Update your `.env` file with your database credentials.*

4. Run Migrations & Seeders
   ```bash
   php artisan migrate --seed
   ```
5. Serve the application
   ```bash
   php artisan serve
   ```

## 🗄️ Database Schema
- `users`
- `categories`
- `menus`
- `orders`
- `order_items`
- `carts`
- `banners`

## 🌐 Related Projects
- 🖥️ Admin Dashboard: [github.com/sukunenv/picpic-cafe-admin](https://github.com/sukunenv/picpic-cafe-admin)
- 📱 Customer App: [github.com/sukunenv/picpic-cafe-web](https://github.com/sukunenv/picpic-cafe-web)

## 👨💻 Developer
Built with 🔥 by Kalify.dev

## 📄 License
Proprietary — Picpic Cafe © 2026
