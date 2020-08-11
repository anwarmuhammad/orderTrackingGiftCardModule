<?php

use yii\helpers\Html;
use unlock\modules\giftcard\models\GiftCard;
use unlock\modules\supplier\models\Supplier;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\supplier\models\GiftCardSeller;
use unlock\modules\purchaseorder\models\Transaction;

    $supplier = Supplier::find()
    ->select('image, name')
    ->where(['id' => $model->supplier_id])
    ->one();
    // echo $supplier->image;
?>
<div class="supplier-item  giftcard">
    <p
            data-href="<?= \yii\helpers\Url::toRoute(['/inbox-order/inbox-order-summary/update', 'docId' => $model->doc_id]) ?>"
            data-doc-id="<?=  $model->doc_id?>"
            data-order-id="<?=  $model->id?>"
            data-model-relational-data="<?=  $modelRelationalData = GiftCard::getGiftCardRelationalData($model['id'])?>"
            data-model-status="<?= $model->status?>" >

    </p>
    <div class="row">
        <div class="col-md-8">
            <p class="supplier-number"><?= Html::encode($model->gift_card_number) ?></p>

            <?php

            $giftCardSeller = GiftCardSeller::getSellerGiftCardInfoBySellerId($model->gift_card_seller_id);

            ?>

            <p><?php if(!empty($giftCardSeller)){$giftCardSeller->name;}?></p>

            <?php
            $purchaseDate = $model->purchased_date;
            if($purchaseDate)
            {
                $newDate = date("m/d/Y", strtotime($purchaseDate));
                ?>
                    <p class="supplier-date"><i class="fas fa-calendar-alt"></i> <i><?=$newDate?></i></p>
                <?php
            }
            else{
                ?>
                    <p class="supplier-date">&nbsp;</p>
                <?php
            }


            ?>
        </div>
        <div class="col-md-4">
        <?php
            if(isset($supplier->image)) { ?>
                <img src="<?= Yii::getAlias('@baseUrl') ?>/media/supplier/<?php echo $supplier->image;?>" alt="<?php echo $supplier->name; ?>"  class="img-fluid">
            <?php }else{ ?>
                <img src="<?= Yii::getAlias('@web/themes/espiralo') ?>/images/blank-image.png"  alt="Gift Card" class="img-fluid" />
        <?php } ?>
        </div>
    </div>

    <!-- <span class="supplier-tag">Tag 1</span><span class="supplier-tag">Another Tag</span> -->
     
    <hr style="margin-top: 0rem;">
    <ul>
        <li>
            <p>Price</p>
            <p class="supplier-price-value">$<?= number_format($model->price, 2); ?></p>
        </li>
        &nbsp;
        <li>
            <p>Balance</p>

            <?php

            $giftCardBalance = Transaction::balanceCalculation(CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, CommonHelper::ACCOUNT_TYPE_GIFT_CARD, $model->id);
            ?>
            <p class="supplier-price-value">$<?= number_format($giftCardBalance, 2); ?></p>
        </li>
        &nbsp; &nbsp;
        <li>
            <p>Discount</p>
            <?php

            $giftCardCumulativeDiscount = Transaction::cumulativeDiscountCalculationByGiftCardId(CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, CommonHelper::ACCOUNT_TYPE_GIFT_CARD, $model->id);
            ?>
            <p class="supplier-price-value"><?= number_format($giftCardCumulativeDiscount*100, 2)." %"; ?></p>
        </li>
    </ul>

    <ul>
        <li style="float: right;margin: 0">
            <div style="margin-top: 10px;" class="status-wrapper">
                <?php

//                if($model['status'] == CommonHelper::STATUS_ACTIVE){
//                    ?>
<!--                    <span class="status-active">Active</span>-->
<!--                    --><?php
//                }elseif ($model['status'] == CommonHelper::STATUS_INACTIVE){
//                    ?>
<!--                    <span class="status-inactive">Inactive</span>-->
<!--                    --><?php
//                }

                if($model['status'] == CommonHelper::STATUS_INACTIVE){
                    ?>
                    <span class="status-inactive">Inactive</span>
                    <?php
                }

                ?>
            </div>
        </li>
    </ul>
    &nbsp;&nbsp;
    <!--<div>
        <span class="supplier-tag">Tag 1</span><span class="supplier-tag">Another Tag</span>
    </div>-->


</div>
