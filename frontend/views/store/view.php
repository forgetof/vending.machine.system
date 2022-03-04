<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use backend\models\ItemSearch;

/* @var $this yii\web\View */
/* @var $model common\models\Store */

$this->title = 'Vending Machine';
?>
<div id="store-vue" class="store-view">

    <template v-if="has_items" v-for="item in items">
        <div class="col-sm-3 col-xs-6 box_row">
            <div class="box_item">
                <a @click="createPayment(item.id,item.amount)">

                    <div class="box-code-id text-center b-color">
                        <div class="box-number">{{item.code}}</div>
                    </div>

                    <div class="row item_image">
                        <img :src="item.image" class="img-responsive center-block" />
                    </div>

                    <div class="row item_name text-center">
                        {{item.name}}
                    </div>

                    <div class="row text-center item-price">
                        <span class="item_price font-color">
                            RM {{item.amount}}
                        </span>
                    </div>

                    <div class="box-buy text-center b-color">
                        BUY
                    </div>

                </a>
            </div>
        </div>
    </template>

    <template v-if="!has_items">
        <h3 class="text-center h-color">
            Out of Stock
        </h>
    </template>

    <div v-if="isLoading" style="position:fixed;top:0;bottom:0;left:0;right:0;background-color:rgba(0,0,0,0.7);">
        <div class="text-center" style="color:white;padding-top:20vh">
            <br>
            <br>
            <br>
            <p class="text-muted">
                <i class="fa fas fa-spinner fa-3x fa-spin" style="color:white"></i>
            </p>
            <br>
            <p class="text-emphasis">Processing your request...</p>
        </div>
    </div>
</div>

<?php
$js = <<< JS
var device_tag = '';

// Do not change the function name, this function will be called by Native APP after payment
function getDeviceTag(message) {
    var param = JSON.parse(message);
    console.log(param.deviceTag);
    device_tag = param.deviceTag;
}

// Do not change the function name, this function will be called by Native APP after payment
function updateStatus(message) {
    var param = JSON.parse(message);

    setTimeout(function() {
        console.log('should wait 1 s');
        store_vue.updateInfo(param.order_id);
    }, 1000);
}
JS;

$this->registerJs($js, \yii\web\View::POS_HEAD);
?>

<?php
$vue_js = <<< JS

var store_id = '$store_id';
store_vue = new Vue({
    el: '#store-vue',
    data: {
        isLoading: false,
        items:[],
        has_items: true
    },
    created() {
        this.fetchData()
    },
    methods: {
        fetchData() {
            fetch('/api/get-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: store_id,
                })
            }).then(response => {
                return response.json();
            }).then(data => {
                this.items = data;

                if (data.code == -1) {
                    this.has_items = false;
                }

            }).catch(error => {
                console.log(error);
            });
        },
        createPayment(item_id, amount) {
            if (this.isLoading) {
                // prevent creating multiple transaction request
                return false;
            }

            this.isLoading = true;

            fetch('https://api.payandgo.link/payment?device_tag=' + device_tag, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    merchant_id: "manapharmacy",
                    amount: amount,
                    name: "Vending Machine Payment",
                    remark: "Purchases from Vending Machine",
                })
            }).then(response => {
                return response.json();
            }).then(data => {
                this.createSaleRecord(data.order_id, item_id);
            }).catch(error => {
                console.log(error);
            });
        },
        makePayment(order_id) {
            var params = {
                order_id: order_id,
                method: 'showCheckout'
            };
            checkout.postMessage(JSON.stringify(params));
        },
        createSaleRecord(order_id, item_id) {
            fetch('/api/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    reference_no: order_id,
                    item_id: item_id,
                })
            }).then(response => {
                return response.json();
            }).then(data => {
                this.makePayment(order_id);
            }).catch(error => {
                console.log(error);
            }).finally(() => {
                this.isLoading = false;
            });
        },
        updateInfo(order_id) {
            fetch('https://api.payandgo.link/payment/view?order_id=' + order_id, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            }).then(response => {
                return response.json();
            }).then(data => {
                // do something here
                location.reload();
            }).catch(error => {
                console.log(error);
            }).finally(() => {
                this.isLoading = false;

            });
        }
    },
});
JS;

$this->registerJs($vue_js);
?>