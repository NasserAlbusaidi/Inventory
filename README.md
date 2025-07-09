# Inventory Management System

## Overview
This project is an Inventory Management System built using Laravel and Livewire. It provides functionalities to manage products, categories, suppliers, purchase orders, sales orders, and inventory movements. The system is designed to be user-friendly and responsive, ensuring seamless management of inventory-related tasks.

## Features
- **Product Management**: Add, edit, delete, and filter products by category, status, and stock levels.
- **Category Management**: Create and manage product categories.
- **Supplier Management**: Manage suppliers and their details.
- **Purchase Orders**: Create and track purchase orders.
- **Sales Orders**: Manage sales orders and track their status.
- **Inventory Movements**: Track inventory movements across locations.
- **Stock Adjustment**: Adjust stock levels for products and variants.
- **Flash Messages**: Display success, error, and warning messages.
- **Pagination**: Paginate product lists for better navigation.
- **Responsive Design**: Optimized for both desktop and mobile views.


## Technologies Used
- **Backend**: Laravel
- **Frontend**: Blade templates, Livewire
- **Database**: MySQL
- **Styling**: Tailwind CSS
- **JavaScript**: Alpine.js

## Installation
1. Clone the repository:
   ```bash
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```bash
   cd Inventory
   ```
3. Install dependencies:
   ```bash
   composer install
   npm install
   ```
4. Set up the environment file:
   ```bash
   cp .env.example .env
   ```
   Update the `.env` file with your database credentials.
5. Generate the application key:
   ```bash
   php artisan key:generate
   ```
6. Run migrations:
   ```bash
   php artisan migrate
   ```
7. Seed the database (optional):
   ```bash
   php artisan db:seed
   ```
8. Start the development server:
   ```bash
   php artisan serve
   ```

## Usage
- Access the application at `http://localhost:8000`.
- Use the navigation menu to manage products, categories, suppliers, and orders.
- Utilize filters and search functionalities to find specific products or orders.

## Project Structure
- **app/**: Contains the core application logic, including models, controllers, and services.
- **resources/views/**: Blade templates for the frontend.
- **routes/**: Application routes.
- **database/**: Migrations, seeders, and factories.
- **public/**: Public assets like CSS, JS, and images.
- **tests/**: Feature and unit tests.

## Contributing
Contributions are welcome! Please follow these steps:
1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature-name
   ```
3. Make your changes and commit them:
   ```bash
   git commit -m "Description of changes"
   ```
4. Push to your fork:
   ```bash
   git push origin feature-name
   ```
5. Create a pull request.



## Contact
For any inquiries or support, please contact [nasserbusaidi@example.com].
