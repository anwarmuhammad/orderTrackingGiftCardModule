<?php
use yii\helpers\Html;
use unlock\modules\purchaseorder\models\Transaction;
?>
<div class="col-sm-3" >
    <div class="kanban-item" data-kanban-item-id="<?= $model->id ?>" data-kanban-item-doc-id="<?= $model->doc_id ?>">
        <h2><?= Html::encode($model->gift_card_number) ?></h2>
        <p>
            <?php
            $giftCardBalance = Transaction::getGiftCardBalanceByPurchasingAccount($model->id);
            ?>
            Balance: $<?= number_format($giftCardBalance, 2); ?><br>
            Price: $<?= number_format($model->price, 2); ?><br>

            Discount: <?= number_format($model->discount_rate, 2)." %"; ?>
          
        </p>
    </div>
</div>