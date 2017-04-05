<?php

$params = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'HD-basic-tests',
    'basePath' => dirname(__DIR__),    
    'language' => 'en-US',
    'components' => [
        'db' => require __DIR__ . '/db-test.php',
        'mailer' => [
            'useFileTransport' => true,
        ],
        'assetManager' => [            
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'user' => [
            'identityClass' => app\models\User::class,
        ],        
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            // but if you absolutely need it set cookie domain to localhost
            /*
            'csrfCookie' => [
                'domain' => 'localhost',
            ],
            */
        ],        
    ],
    'params' => $params,
];
