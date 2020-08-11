<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use unlock\modules\giftcard\models\GiftCard;
use unlock\modules\supplier\models\Supplier;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\supplier\models\GiftCardSeller;
use unlock\modules\purchaseorder\models\Transaction;


$models = $dataProvider->getModels();

?>
<?php
//Columns must be a factor of 12 (1,2,3,4,6,12)
$numOfCols = 4;
$rowCount = 0;
$bootstrapColWidth = 12 / $numOfCols;
?>
<div class="row">
    <?php
    if(!empty($models)){

        foreach ($models as $model) { 

            $supplier = Supplier::find()
            ->select('image, name')
            ->where(['id' => $model->supplier_id])
            ->one();
            // echo $supplier->image;
            
            ?>
            
            <div class="col-md-<?php echo $bootstrapColWidth; ?>" data-key="<?= $model->id ?>">
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

                            <p>
                                <?php
                                if($giftCardSeller){
                                    $giftCardSeller->name;
                                }
                                ?>
                            </p>

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
                            if(isset($supplier->image)){?>
                                <img src="<?= Yii::getAlias('@baseUrl') ?>/media/supplier/<?php echo $supplier->image;?>" alt="<?php echo $supplier->name; ?>" class="img-fluid">
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

                            $giftCardBalance = Transaction::getGiftCardBalanceByPurchasingAccount($model->id);
                            ?>
                            <p class="supplier-price-value">$<?= number_format($giftCardBalance, 2); ?></p>
                        </li>
                        &nbsp; &nbsp;
                        <li>
                        <?php

                        $giftCardCumulativeDiscount = Transaction::cumulativeDiscountCalculationByGiftCardId(CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, CommonHelper::ACCOUNT_TYPE_GIFT_CARD, $model->id);
                        ?>
                            <p>Discount</p>
                            <p class="supplier-price-value"><?= number_format($giftCardCumulativeDiscount*100, 2)." %"; ?></p>
                        </li>
                    </ul>
                    <ul>
                        <li style="float: right;margin: 0">
                            <div style="margin-top: 10px;" class="status-wrapper">
                                <?php

//                                if($model['status'] == CommonHelper::STATUS_ACTIVE){
//                                    ?>
<!--                                    <span class="status-active">Active</span>-->
<!--                                    --><?php
//                                }elseif ($model['status'] == CommonHelper::STATUS_INACTIVE){
//                                    ?>
<!--                                    <span class="status-inactive">Inactive</span>-->
<!--                                    --><?php
//                                }

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
                   <!-- <div>
                        <span class="supplier-tag">Tag 1</span><span class="supplier-tag">Another Tag</span>

                    </div>-->



                </div>
            </div>
            <?php
            $rowCount++;
            if($rowCount % $numOfCols == 0){echo '</div><div class="row">';}
        }}else{
        ?>
        <div class="col-md-12 text-center"><h5>Gift Card Not Found!</h5></div>
        <?php
    }
    ?>
</div>


<script>
    $(function() {
        $(".supplier").on('dblclick click',function(e){
            $("#add-giftcardsellerModalContent").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");
            $('#header-content').html('Update Gift Card Seller');
            var loaded_url = $(this).attr("data-href");
            //alert(loaded_url);
            $('#giftcardsellerContent').modal('show')
                .find('#add-giftcardsellerModalContent')
                .load(loaded_url);
        });
        
        $('#close-button-n').off('click', '#close-button-n').click(function(){
            $('#giftcardsellerContent').modal('hide');
        });
    });

    $(document).off('click', '.giftcard').on('click', '.giftcard', function(e){

        if($(this).hasClass('active'))
        {
            $("#select-button").hide();
            $("#default-button").show();
            $(this).removeClass('active');
        }
        else{
            $('.giftcard').removeClass('active');
            $("#default-button").hide();
            $("#select-button").show();
            $(this).addClass('active');

            $("#giftCardModal #inbox-content-view").html('');
            $("#giftCardModal #inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");

            var type = e.type;
            var docId = $(this).find('p').data('doc-id');
            var orderId = $(this).find('p').data('order-id');
            var modelRelationalData = $(this).find('p').data('model-relational-data');
            var status = $(this).find('p').data('model-status');

            var deactivestatus = $(this).find('.status-inactive').text();
            var activetatus = $(this).find('.status-active').text();

            if(status == 2){
                var activeButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card/change-status'])?>?docId=' + docId +'&status=1';

                $('#kanban-button-deactive').attr('href', activeButtonUrl);
                $('#kanban-button-deactive').text('Active');
                $('#kanban-button-deactive').css({"background": "#8c368c", "border": "1px solid #8c368c"});

                $("#kanban-button-create-charge").hide();
                $("#kanban-button-create-payment").hide();
                $("#kanban-button-create-reload").hide();
            }
            if(status == 1){
                var deactiveButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card/change-status'])?>?docId=' + docId +'&status=2';

                $('#kanban-button-deactive').attr('href', deactiveButtonUrl);
                $('#kanban-button-deactive').text('Deactivate');
                $('#kanban-button-deactive').css({"background": "#888888", "border": "1px solid #888888"});

                $("#kanban-button-create-charge").show();
                $("#kanban-button-create-payment").show();
                $("#kanban-button-create-reload").show();
            }
            if(modelRelationalData == '1'){
                $('#kanban-button-delete').hide();

            }else{
                $('#kanban-button-delete').show();
            }

            var deleteButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card/delete'])?>?docId=' + docId;
            $('#kanban-button-delete').attr('href', deleteButtonUrl);


            // $('.giftcard').removeClass('active');
            // $(this).addClass('active');
            // $("#default-button").hide();
            // $("#select-button").show();

            $("#kanban-button-create-reload").on('click', function (e) {

                $("#add-giftCardTransactionModalContent").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");

                var reloadButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card-reload/create'])?>?refDocId=' + docId;
                $("h4.modal-title.dynamic-title").html("<span class='payment-account-modal-title'>"+"Reload "+"</span>");
                $('#giftCardTransactionModalContent').modal('show')
                    .find('#add-giftCardTransactionModalContent')
                    .load(reloadButtonUrl);

            });

            $("#kanban-button-create-payment").on('click', function (e) {

                $("#inbox-content-view").html('');
                $("#inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");

                var paymentButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card-payment/create'])?>?refDocId=' + docId;
                // $('#kanban-button-create-payment').attr('href', paymentButtonUrl);
                $("h4.modal-title.dynamic-title").html("<span class='payment-account-modal-title'>"+"Pay Gift Card"+"</span>");
                $('#inboxModal').modal('show')
                .find('#inbox-content-view')
                .load(paymentButtonUrl);

            });

            $("#kanban-button-create-charge").on('click', function (e) {

                $("#inbox-content-view").html('');
                $("#inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");

                var chargeButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card-charge/create'])?>?refDocId=' + docId;
                // $('#kanban-button-create-charge').attr('href', chargeButtonUrl);
                $("h4.modal-title.dynamic-title").html("<span class='payment-account-modal-title'>"+"Charge "+"</span>");
                $('#inboxModal').modal('show')
                .find('#inbox-content-view')
                .load(chargeButtonUrl);

                });
            $(document).on('click','#kanban-button-view-single-transaction',function(e){
                var viewTransactionButtonUrl = '<?= Url::toRoute(['/payment-method/payment-method-transaction/gift-card'])?>?accountId=' + orderId;
                $('#kanban-button-view-single-transaction').attr('href', viewTransactionButtonUrl);

            });
        }
        
        });

    $(document).off('dblclick', '.giftcard').on('dblclick', '.giftcard', function (event) {
        $("#inbox-content-view").html('');
        $("#inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");

        var docId = $(this).find('p').data('doc-id');

        var updateButtonUrl = '<?= Url::toRoute(['update'])?>?docId=' + docId;


        $('#inboxModal').modal('show')
        .find('#inbox-content-view')
        .load(updateButtonUrl);
        
    });
</script>