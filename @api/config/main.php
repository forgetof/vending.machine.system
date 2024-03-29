<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG,
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'payment',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET view'          => 'view',
                        'GET reference'     => 'reference',
                        'POST signalling'   => 'signalling',
                        'POST create'       => 'create',
                        'OPTIONS <action:[\w-]+>' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'kiosk',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET request'   => 'request',
                        'GET next'      => 'next',
                        'OPTIONS <action:[\w-]+>' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'cron',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST minute' => 'minute',
                        'OPTIONS <action:[\w-]+>' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'box',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST store-item-into-box'   => 'store-item-into-box',
                        'POST open-box'      => 'open-box',
                        'OPTIONS <action:[\w-]+>' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'test',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST open-box'  => 'open-box',
                        'GET open-all'  => 'open-all',
                        'OPTIONS <action:[\w-]+>' => 'options',
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
];
