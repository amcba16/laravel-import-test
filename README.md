<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

README

Shopify Product Importer (CSV to Shopify)

This Laravel application reads product data from a CSV file, imports the products into Shopify, and then adds the created products to a specific Shopify collection.

Features

Import product data from a CSV file

Create Shopify products using the Shopify Admin API

Add all imported products to a Shopify collection

Simple dashboard with CSV upload

User authentication included

Installation Steps

Clone the repository:
git clone https://github.com/amcba16/laravel-import-test.git

Go to the project directory:
cd laravel-import-test

Install PHP dependencies:
composer install

Copy environment variables:
Open the .env.example file and copy only the last 4 variables (Shopify credentials)
Paste them into your .env file.
These include:
SHOPIFY_STORE_DOMAIN
SHOPIFY_ACCESS_TOKEN
SHOPIFY_API_VERSION
SHOPIFY_COLLECTION_ID

Run the Composer development script:
composer run dev

Install and build frontend dependencies:
npm install
npm run dev
npm run build

Run database migrations:
php artisan migrate

Start the application:
php artisan serve

Register a new user:
After the server starts, open the app in the browser, create a new account, go to the dashboard, and upload your CSV file.

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
