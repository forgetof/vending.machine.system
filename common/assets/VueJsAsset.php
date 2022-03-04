<?php

namespace common\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class VueJsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.10/vue.min.js',
        // 'cdn/vue/vue.min.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
