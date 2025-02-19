<?php

return [
    'frontend' => [
        'test/sitepackage' => [
            'target' => \Test\Sitepackage\Middleware\DemoMiddleware::class,
            'after' => [
                'typo3/cms-core/response-propagation',
            ],
        ],
    ],
];
