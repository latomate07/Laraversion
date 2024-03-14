<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Max Versions
    |--------------------------------------------------------------------------
    |
    | The maximum number of versions to keep for each model.
    |
    */
    'max_versions' => 3,

    /*
    |--------------------------------------------------------------------------
    | Listen Events
    |--------------------------------------------------------------------------
    |
    | The events to listen for versioning on all models.
    |
    */
    'listen_events' => [
        'created',
        'updated',
        'deleted',
        'restored',
        'forceDeleted',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models Configuration
    |--------------------------------------------------------------------------
    |
    | The models to version and their specific configuration.
    |
    */
    'models' => [
        // 'App\Models\YourModel' => [
        //     'max_versions' => 5,
        //     'listen_events' => [
        //         'created',
        //         'updated',
        //     ],
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Load Views and Routes
    |--------------------------------------------------------------------------
    |
    | Specify whether to load views and routes provided by the Laraversion package.
    | Set this option to 'true' to load views and routes, or 'false' to skip loading them.
    |
    */
    'load_views_and_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | The middleware to use for Laraversion routes.
    |
    */
    'middleware' => [
        'web',
        // more middleware...
    ],
];