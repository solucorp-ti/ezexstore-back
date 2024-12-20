<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Product Configuration
    |--------------------------------------------------------------------------
    */

    // Default pagination size
    'per_page' => env('PRODUCT_PER_PAGE', 15),

    // Maximum number of products per tenant
    'max_per_tenant' => env('PRODUCT_MAX_PER_TENANT', 1000),

    // Maximum file size for product photos in kilobytes
    'max_photo_size' => env('PRODUCT_MAX_PHOTO_SIZE', 2048), // 2MB
    
    // Allowed photo extensions
    'allowed_photo_extensions' => ['jpg', 'jpeg', 'png', 'webp'],
];