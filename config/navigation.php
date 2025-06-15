<?php
return [
    [
        'name' => 'Catalog',
        'routes' => ['products.*', 'categories.*', 'locations.*'],
        'children' => [
            ['name' => 'Products', 'route' => 'products.index'],
            ['name' => 'Categories', 'route' => 'categories.index'],
            ['name' => 'Locations', 'route' => 'locations.index'],
        ],
    ],
    [
        'name' => 'Operations',
        'routes' => ['purchase-orders.*', 'sales-orders.*', 'suppliers.*'],
        'children' => [
            ['name' => 'Purchase Orders', 'route' => 'purchase-orders.index'],
            ['name' => 'Sales Orders', 'route' => 'sales-orders.index'],
            ['name' => 'Suppliers', 'route' => 'suppliers.index'],
        ],
    ],
    [
        'name' => 'Settings',
        'routes' => ['expenses.*', 'inventory.adjustments.*'],
        'children' => [
            ['name' => 'Expenses', 'route' => 'expenses.index', 'active' => 'expenses.*'],
            ['name' => 'Settings', 'route' => 'settings.index', 'active' => 'settings.*'],
        ],
    ],
];
