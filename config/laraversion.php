<?php

return [
    /*
     * The maximum number of versions to keep for each model.
     */
    'max_versions' => 3,

    /*
     * The events to listen for versioning on all models.
     */
    'listen_events' => [
        'created',
        'updated',
        'deleted',
        'restored',
        'forceDeleted',
    ],

    /*
     * The models to version and their specific configuration.
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
];