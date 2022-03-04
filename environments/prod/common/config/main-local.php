<?php
return [
    'aliases' => [
        '@upload'       => '/app/backend/web/mel-img/',
        '@imagePath'    => 'https://localhost:21110/products',
        '@urlBackend'   => 'https://vm-admin.payandgo.link/',
        '@urlFrontend'  => 'https://vm.payandgo.link/',
        '@static'       => 'https://s3-ap-southeast-1.amazonaws.com/cdn.payandgo.link/',
    ],
    'components' => [
        'spay' => [
            'class'                     => 'common\components\SarawakPay',
            'merchantId'                => 'M100001040',
            'url'                       => 'https://spfintech.sains.com.my/xservice/',
            'privateKeyPath'            => '@common/plugins/spay/merchant_private_key.key',
            'publicKeyPath'             => '@common/plugins/spay/merchant_public_key.key',
            'sarawakPayPublicKeyPath'   => '@common/plugins/spay/sarawakpay_public_key.pem',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        '//cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js',
                        'integrity' => 'sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==',
                        'crossorigin' => 'anonymous',
                    ],
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [
                        '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/css/bootstrap.min.css',
                    ],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [
                        [
                            '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js',
                            'integrity' => 'sha512-oBTprMeNEKCnqfuqKd6sbvFzmFQtlXS3e0C/RGFV0hD6QzhHV+ODfaQbAlmY6/q0ubbwlAM/nCJjkrgA3waLzg==',
                            'crossorigin' => 'anonymous',
                        ],
                    ],
                ],
                'common\assets\AppAsset' => [
                    'css' => [
                        '//s3-ap-southeast-1.amazonaws.com/cdn.payandgo.link/css/site.css',
                        '//s3-ap-southeast-1.amazonaws.com/cdn.payandgo.link/css/card.css',
                        '//s3-ap-southeast-1.amazonaws.com/cdn.payandgo.link/css/grid.css',
                        '//s3-ap-southeast-1.amazonaws.com/cdn.payandgo.link/css/nav.css',
                    ],
                ],
            ],
        ],
    ],
];
