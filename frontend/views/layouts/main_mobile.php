<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\assets\AppAsset;
use common\assets\FontAwesomeAsset;
use common\assets\VueJsAsset;
use common\widgets\Alert;
use yii\helpers\BaseUrl;

AppAsset::register($this);
FontAwesomeAsset::register($this);
VueJsAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <style>
        .wrap>.container {
            padding: 20px 15px 20px;
        }
    </style>
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="wrap c-color">
        <div class="container">
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>