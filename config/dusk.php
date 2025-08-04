<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dusk Test Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for Laravel Dusk E2E tests
    |
    */

    'default_wait_time' => env('DUSK_WAIT_TIME', 5),
    
    'implicit_wait' => env('DUSK_IMPLICIT_WAIT', 10),
    
    'page_load_timeout' => env('DUSK_PAGE_LOAD_TIMEOUT', 30),
    
    'script_timeout' => env('DUSK_SCRIPT_TIMEOUT', 30),
    
    'browser_size' => [
        'width' => env('DUSK_BROWSER_WIDTH', 1920),
        'height' => env('DUSK_BROWSER_HEIGHT', 1080),
    ],
    
    'mobile_sizes' => [
        'iphone_se' => ['width' => 375, 'height' => 667],
        'iphone_12' => ['width' => 390, 'height' => 844],
        'ipad' => ['width' => 768, 'height' => 1024],
        'ipad_pro' => ['width' => 1024, 'height' => 1366],
    ],
    
    'test_data' => [
        'admin_user' => [
            'email' => 'admin@dttbylogix.test',
            'password' => 'password123',
            'role' => 'admin',
        ],
        'regular_user' => [
            'email' => 'user@dttbylogix.test',
            'password' => 'password123',
            'role' => 'user',
        ],
    ],
    
    'selectors' => [
        // Navigation
        'navigation_menu' => '[data-testid="navigation-menu"]',
        'mobile_menu_toggle' => '[data-testid="mobile-menu-toggle"]',
        'mobile_menu_close' => '[data-testid="mobile-menu-close"]',
        'user_menu' => '[data-testid="user-menu"]',
        
        // Documents
        'document_list' => '[data-testid="document-list"]',
        'create_document_btn' => '[data-testid="create-document-btn"]',
        'edit_document_btn' => '[data-testid="edit-document-btn"]',
        'delete_document_btn' => '[data-testid="delete-document-btn"]',
        'export_csv_btn' => '[data-testid="export-csv-btn"]',
        'search_input' => '[data-testid="search-input"]',
        'category_filter' => '[data-testid="category-filter"]',
        'pagination' => '[data-testid="pagination"]',
        'next_page_btn' => '[data-testid="next-page-btn"]',
        'prev_page_btn' => '[data-testid="prev-page-btn"]',
        
        // Forms
        'document_form' => '[data-testid="document-form"]',
        'title_input' => 'input[name="title"]',
        'description_input' => 'textarea[name="description"]',
        'category_select' => 'select[name="category"]',
        'supplier_input' => 'input[name="supplier"]',
        'file_input' => 'input[name="file"]',
        'save_btn' => '[data-testid="save-btn"]',
        'cancel_btn' => '[data-testid="cancel-btn"]',
        
        // Barcode Scanner
        'manual_barcode_input' => '[data-testid="manual-barcode-input"]',
        'search_barcode_btn' => '[data-testid="search-barcode-btn"]',
        'scanner_component' => '[data-testid="scanner-component"]',
        'camera_toggle_btn' => '[data-testid="camera-toggle-btn"]',
        
        // Loading and feedback
        'loading_indicator' => '[data-testid="loading-indicator"]',
        'saving_indicator' => '[data-testid="saving-indicator"]',
        'search_loading' => '[data-testid="search-loading"]',
        'success_message' => '[data-testid="success-message"]',
        'error_message' => '[data-testid="error-message"]',
        
        // Accessibility
        'main_content' => '[role="main"]',
        'navigation_bar' => '[role="navigation"]',
        'logo_image' => '[data-testid="logo-image"]',
        'search_icon' => '[data-testid="search-icon"]',
        
        // Error pages
        'back_to_documents_btn' => '[data-testid="back-to-documents-btn"]',
        'error_page_content' => '[data-testid="error-page-content"]',
        
        // Document details
        'barcode_display' => '[data-testid="barcode-display"]',
        'document_details' => '[data-testid="document-details"]',
        'file_download_btn' => '[data-testid="file-download-btn"]',
        'regenerate_barcode_btn' => '[data-testid="regenerate-barcode-btn"]',
        
        // Validation
        'title_error_message' => '[data-testid="title-error"]',
        'description_error_message' => '[data-testid="description-error"]',
        'category_error_message' => '[data-testid="category-error"]',
        'file_error_message' => '[data-testid="file-error"]',
    ],
    
    'test_files' => [
        'sample_pdf' => storage_path('app/testing/sample.pdf'),
        'sample_image' => storage_path('app/testing/sample.jpg'),
        'large_file' => storage_path('app/testing/large_file.pdf'),
    ],
    
    'performance_thresholds' => [
        'page_load_time' => 5, // seconds
        'csv_export_time' => 10, // seconds
        'search_response_time' => 2, // seconds
        'form_submission_time' => 5, // seconds
        'memory_limit' => 50 * 1024 * 1024, // 50MB
    ],
    
    'accessibility' => [
        'test_keyboard_navigation' => env('DUSK_TEST_KEYBOARD', true),
        'test_screen_reader' => env('DUSK_TEST_SCREEN_READER', true),
        'test_color_contrast' => env('DUSK_TEST_CONTRAST', true),
        'test_aria_labels' => env('DUSK_TEST_ARIA', true),
    ],
    
    'browser_options' => [
        'headless' => env('DUSK_HEADLESS', true),
        'disable_gpu' => env('DUSK_DISABLE_GPU', true),
        'no_sandbox' => env('DUSK_NO_SANDBOX', false),
        'disable_dev_shm_usage' => env('DUSK_DISABLE_DEV_SHM', true),
        'disable_extensions' => env('DUSK_DISABLE_EXTENSIONS', true),
        'disable_web_security' => env('DUSK_DISABLE_WEB_SECURITY', false),
    ],
    
    'screenshots' => [
        'on_failure' => env('DUSK_SCREENSHOT_ON_FAILURE', true),
        'directory' => env('DUSK_SCREENSHOT_DIR', 'tests/Browser/screenshots'),
        'format' => env('DUSK_SCREENSHOT_FORMAT', 'png'),
    ],
    
    'console_logs' => [
        'capture' => env('DUSK_CAPTURE_CONSOLE', true),
        'directory' => env('DUSK_CONSOLE_LOG_DIR', 'tests/Browser/console'),
    ],
];