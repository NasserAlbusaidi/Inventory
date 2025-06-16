# Inventory Management System

<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

This is a comprehensive and modern inventory management system built with Laravel, Livewire, and Alpine.js, designed to help businesses efficiently track products, manage stock across multiple locations, handle purchase and sales orders, and monitor expenses. The application offers a clean, responsive user interface and robust backend functionalities to streamline inventory operations.

## Features

* **Dashboard & Analytics**:
    * Real-time financial KPIs (Total Revenue, Total Costs, Net Profit, Profit Margin).
    * Sales KPIs (Sales Count, Average Order Value, New Customers).
    * Inventory & Purchasing KPIs (Total Stock Units, Inventory Value, Low/Out of Stock Products).
    * Configurable low stock thresholds and revenue targets.
* **Product Management**:
    * Create, view, edit, and delete products.
    * Support for products with and without variants (e.g., different sizes, colors).
    * Bulk import products via CSV/Excel file with robust error handling.
* **Inventory Control**:
    * Track stock quantities per product variant and location.
    * Manual stock adjustments (additions, deductions, setting new quantity).
    * Detailed inventory movement history.
* **Order Management**:
    * Create and manage Purchase Orders (PO) with multiple items and statuses (draft, ordered, received, cancelled).
    * Create and manage Sales Orders (SO) across various channels (e.g., Website, Boutique, Instagram).
    * Automated stock deduction upon sales order fulfillment and addition upon purchase order receipt.
* **Supplier & Location Management**:
    * Manage supplier information.
    * Define and manage multiple inventory locations.
* **Expense Tracking**:
    * Record and manage both recurring and one-time expenses associated with locations.

## Technologies Used

* **Backend**:
    * PHP 8.2+
    * Laravel 12.x
    * Livewire 3.x for dynamic interfaces
    * Doctrine DBAL for database schema manipulation
    * Maatwebsite/Excel for importing products
* **Frontend**:
    * Alpine.js for reactive templating.
    * Tailwind CSS for styling.
    * Vite for asset bundling.
* **Database**:
    * MySQL, PostgreSQL, or SQLite compatible (configured in `config/database.php`).

## Installation

Follow these steps to get the project up and running on your local machine.

### Prerequisites

* PHP >= 8.2
* Composer
* Node.js & npm (or Yarn)
* A database (MySQL, PostgreSQL, or SQLite recommended)

### Steps

1.  **Clone the repository**:
    ```bash
    git clone [https://github.com/nasseralbusaidi/inventory.git](https://github.com/nasseralbusaidi/inventory.git)
    cd inventory
    ```

2.  **Install PHP Dependencies**:
    ```bash
    composer install
    ```

3.  **Install JavaScript Dependencies**:
    ```bash
    npm install
    # OR
    # yarn install
    ```

4.  **Create and Configure your Environment File**:
    Copy the `.env.example` file to `.env`:
    ```bash
    cp .env.example .env
    ```
    Open `.env` and configure your database connection (e.g., `DB_CONNECTION=mysql`, `DB_DATABASE=your_db_name`, `DB_USERNAME=your_username`, `DB_PASSWORD=your_password`). If using SQLite, ensure `database/database.sqlite` exists and set `DB_CONNECTION=sqlite`.

5.  **Generate Application Key**:
    ```bash
    php artisan key:generate
    ```

6.  **Run Database Migrations and Seeders**:
    This will create the necessary tables and populate them with some initial data (including default sales channels, locations, suppliers, categories, and products).
    ```bash
    php artisan migrate --seed
    ```
    *If you encounter issues with migrations, you might need to install `doctrine/dbal` (already included in `composer.json` but can be installed manually if needed).*

7.  **Symlink Storage (if needed)**:
    ```bash
    php artisan storage:link
    ```

8.  **Build Frontend Assets**:
    ```bash
    npm run build
    # OR
    # yarn build
    ```

9.  **Start the Development Server**:
    ```bash
    php artisan serve
    ```
    The application will typically be available at `http://127.0.0.1:8000`.

## Usage

After installation, you can access the application through your web browser.

* **Navigation**: Use the sidebar and top navigation to access different modules:
    * **Catalog**: Manage Products, Categories, and Locations.
    * **Operations**: Handle Purchase Orders, Sales Orders, and Suppliers.
    * **Expenses**: Track recurring and one-time expenses.
    * **Settings**: Configure application settings like KPI targets.

## Contributing

Contributions are welcome! Please feel free to open issues or submit pull requests.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
