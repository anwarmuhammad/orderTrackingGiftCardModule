<?php
use yii\helpers\Html;
use unlock\modules\purchaseorder\models\Transaction;
?>
<div class="col-md-4">
    <div class="supplier-item active">
        <p class="supplier-number">6333339888778855</p>
        <p>Card Cookie</p>
        <p class="supplier-date"><i class="fas fa-calendar-alt"></i> <i>07/09/2018</i></p>
        <span class="supplier-tag">Tag 1</span><span class="supplier-tag">Another Tag</span>
        <hr>
        <ul>
            <li>
                <p>Price</p>
                <p class="supplier-price-value">$450.00</p>
            </li>
            <li>
                <p>Balance</p>
                <p class="supplier-price-value">$12.09</p>
            </li>
            <li>
                <p>Discount</p>
                <p class="supplier-price-value">10.00%</p>
            </li>
        </ul>                                     
        <img src="<?= Yii::getAlias('@web/themes/espiralo') ?>/images/blank-image.png" alt="" class="img-fluid" />
    </div>
</div>