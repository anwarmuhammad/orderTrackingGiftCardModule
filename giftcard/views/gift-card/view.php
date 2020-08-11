<?php

use yii\helpers\Url;
use yii\helpers\Html;
use unlock\modules\core\widgets\DetailView;
use unlock\modules\core\buttons\BackButton;
use unlock\modules\core\helpers\CommonHelper;

/* @var $this yii\web\View */
/* @var $model unlock\modules\giftcard\models\GiftCard */

$this->title = Yii::t('app', 'View Gift Card');
?>
<div class="main-container main-container-view" role="main">
    <div class="page-header">
        <div class="page-header-title">
            <h1 class="page-title"><?= Yii::t('app', 'Gift Card') ?></h1>
        </div>
        <div class="page-header-breadcrumb">
            <ul class="breadcrumb">
                <li><?= Html::a('Home', Yii::$app->homeUrl, ['class' => '']) ?></li>
                <li><?= Html::a(Yii::t('app', 'Gift Card'), Url::to('index')) ?></li>
                <li class="active"><span><?= Html::encode($this->title) ?></span></li>
            </ul>
        </div>
    </div>

    <div class="admin-view-container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-heading-title">
                    <h3 class="panel-title"><i class="fa fa-eye"></i> <?= Yii::t('app', 'View') ?> - <?= Html::encode($model->id) ?></h3>
                </div>
                <div class="panel-heading-button">
                    <?= BackButton::widget() ?>
                </div>
            </div>

            <div class="panel-body">
                <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                	// 'id',
					// 'org_id',
					// 'order_id',
					// 'doc_id',
					'order_date',
					'supplier',
					'supplier_account',
					'gift_card_seller',
					'order_number',
					// 'card_title',
					'gift_card_number',
					'card_pin',
					'price',
					'value',
					'discount_rate',
					'balance',
					// 'card_sku',
					// 'asin',
					// 'qty',
					// 'discount_amount',
					// 'purchases',
					// 'expiry_date',
					// 'created_by',
					// 'created_at',
					// 'updated_by',
					// 'updated_at',
					// 'status',
                ],
                ]) ?>
            </div>

        </div>
    </div>
</div>
