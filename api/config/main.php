<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

use api\modules\v1\Module;
use common\models\User;
use yii\log\FileTarget;
use yii\rest\UrlRule;
use yii\web\Response;
use yii\web\JsonParser;
use yii\web\JsonResponseFormatter;


return [
    'id'             => 'app-api',
    'basePath'       => dirname(__DIR__),
    'controllerNamespace' => 'api\modules\v1\controllers',
    'bootstrap'      => ['log'],
    'components'     => [
        'request'    => [
            'parsers' => [
                'application/json' => JsonParser::class,
            ],
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => false,
            'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-api',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => false,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => UrlRule::class,
                    'controller' => 'v1/request',
                    //'pluralize'  => false, //убирает множественность в названии контроллера
                    'only'          => [
                       'index',
                    ],
                    'extraPatterns' => [
                        'GET,PUT,POST,DELETE <id:\w+>' => 'index',
                        'GET,PUT,POST,DELETE' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'modules'        => [
        'v1' => [
            'basePath' => '@api/modules/v1',
            'class'    => Module::class,
        ]
    ],
    'params' => $params,
];
