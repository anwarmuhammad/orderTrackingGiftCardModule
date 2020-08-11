<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\jui\DatePicker;
use unlock\modules\core\grid\GridView;
use unlock\modules\core\widgets\Summary;
use unlock\modules\core\widgets\LinkPager;
use unlock\modules\core\buttons\NewButton;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\supplier\models\Supplier;
use unlock\modules\core\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel unlock\modules\giftcard\models\GiftCardSearch */
/* @var $dataProvider unlock\modules\core\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Gift Cards');
?>
<div class="main-container" role="main">
    <div class="page-header">
        <div class="page-header-title">
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="page-header-breadcrumb">
            <ul class="breadcrumb">
                <li><?= Html::a('Home', Yii::$app->homeUrl, ['class' => '']) ?></li>
                <li class="active"><span><?= Html::encode($this->title) ?></span></li>
            </ul>
        </div>
    </div>

    <div class="admin-grid-content">
        <div class="admin-grid-toolbar">
            <div class="admin-grid-toolbar-left">
                <?=Summary::widget(['dataProvider' => $dataProvider])?>
            </div>
        </div>

        <!-- Kanban View -->
        <div class="admin-grid-view admin-kanban-view">
            <div class="row">
                <?php
                echo ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemView' => '_list',
                    'layout' => "{pager}\n{items}",
                    'viewParams' => [
                        'fullView' => true,
                        'context' => 'main-page',
                    ],
                ]);
                ?>
            </div>
        </div>

        <!-- Footer Button -->
        <div class="kanban-footer-button">
            <div class="kanban-button-list before-select-kanban-button">
                <?= Html::a('<i class="fa fa-plus"></i>'.' New Gift Card', Url::toRoute(['/gift-card/gift-card/create']), ['class' => 'btn btn-warning', 'id' => 'kanban-button-add']) ?>
                <?= Html::a('<i class="fa fa-cogs"></i>'.' Customized Field', Url::toRoute(['#']), ['class' => 'btn btn-warning', 'id' => 'kanban-button-add']) ?>
                <?= Html::a('<i class="fa fa-usd"></i> '.' View Transaction', Url::toRoute(['/payment-method/payment-method-transaction/index']) , ['class' => 'btn btn-warning', 'data-url' => Url::toRoute(['update']), 'id' => 'kanban-button-update']) ?>
                <?= Html::a('<i class="fa fa-ellipsis-h"></i>'.' More', Url::toRoute(['#']), ['class' => 'btn btn-warning', 'id' => 'kanban-button-add']) ?>
            </div>
            <div class="kanban-button-list after-select-kanban-button">
                <?= Html::a('<i class="fa fa-repeat"></i> '.' Reload', Url::toRoute(['/gift-card/gift-card-reload/create']), ['class' => 'btn btn-warning', 'data-url' => '', 'id' => 'kanban-button-create-reload']) ?>
                <?= Html::a('<i class="fa fa-credit-card"></i> '.' Pay', Url::toRoute(['/gift-card/gift-card-payment/create']), ['class' => 'btn btn-warning', 'data-url' => '', 'id' => 'kanban-button-create-payment']) ?>
                <?= Html::a('<i class="fa fa-credit-card-alt"></i> '.' Charge', Url::toRoute(['/gift-card/gift-card-charge/create']), ['class' => 'btn btn-warning', 'data-url' => '', 'id' => 'kanban-button-create-charge']) ?>
                <?= Html::a('<i class="fa fa-usd"></i> '.' View Transaction', Url::toRoute(['/payment-method/payment-method-transaction/index']) , ['class' => 'btn btn-warning', 'data-url' => Url::toRoute(['update']), 'id' => 'kanban-button-update']) ?>
                <?= Html::a('<i class="fa fa-ellipsis-h"></i>'.' More', Url::toRoute(['#']), ['class' => 'btn btn-warning', 'id' => 'kanban-button-add']) ?>
            </div>
        </div>

    </div>

</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.kanban-item').on('click', function () {
            var itemId = $(this).data('kanban-item-id');
            var docId = $(this).data('kanban-item-doc-id');

            // Change Button Link
            var updateButtonUrl = '<?= Url::toRoute(['update'])?>?docId=' + docId;
            $('#kanban-button-update').attr('href', updateButtonUrl);

            var reloadButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card-reload/create'])?>?refDocId=' + docId;
            $('#kanban-button-create-reload').attr('href', reloadButtonUrl);

            var paymentButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card-payment/create'])?>?refDocId=' + docId;
            $('#kanban-button-create-payment').attr('href', paymentButtonUrl);

            var chargeButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card-charge/create'])?>?refDocId=' + docId;
            $('#kanban-button-create-charge').attr('href', chargeButtonUrl);

            // Select kanban Item
            if($(this).hasClass('selected-kanban-item')){
                window.location.href = updateButtonUrl;
            }
            else{
                $('.kanban-item').removeClass('selected-kanban-item');
                $(this).addClass('selected-kanban-item');
            }

            // Show Footer Button
            $('.before-select-kanban-button').hide();
            $('.after-select-kanban-button').show();

        });
    });
</script>