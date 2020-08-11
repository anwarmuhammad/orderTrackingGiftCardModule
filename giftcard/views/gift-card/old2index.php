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
use unlock\modules\supplier\models\GiftCardSeller;
use unlock\modules\purchaseorder\models\Transaction;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel unlock\modules\giftcard\models\GiftCardSearch */
/* @var $dataProvider unlock\modules\core\data\ActiveDataProvider */



$this->title = Yii::t('app', 'Gift Cards');
?>

<style type="text/css">

   .select-btn {
    list-style: none;
    display: inline;
    background: var(--mainPurple)!important;
    color: #fff;
    border-radius: 50px;
    padding: 3px 20px;
    font-size: 18px;
    margin: 0 15px;
    cursor: pointer;
}

.btn-info {
    color: #fff;
    background-color: var(--mainPurple);
    border-color: var(--mainPurple);
}

a:hover {
    color: #fff;
    text-decoration: none;
}
.seller-btn ul li, .btn.seller-btn {
    
    background: #8c368c!important;
   
}

</style>

    <div class="main-container" role="main">

            <!-- Content Tab Start -->
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
                       aria-controls="pills-home" aria-selected="true">Suppliers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#" role="tab"
                       aria-controls="pills-profile" aria-selected="false">Suppliers account</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#" role="tab"
                       aria-controls="pills-contact" aria-selected="false">Status</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#" role="tab"
                       aria-controls="pills-contact" aria-selected="false">Tags</a>
                </li>
            </ul>
            <!-- Content Tab End -->

            <!-- Tab Content Start  -->
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

                  <div class="row">
                    <?php foreach($giftCards as $giftCard){ ?>

                        <div class="col-md-4">
                        <div class="supplier-item  giftcard">
                            <p 
                                data-href="<?= \yii\helpers\Url::toRoute(['/inbox-order/inbox-order-summary/update', 'docId' => $giftCard->doc_id]) ?>"
                                data-doc-id="<?=  $giftCard->doc_id?>"
                                data-order-id="<?=  $giftCard->id?>"
                                data-model-status="<?= $giftCard->status?>" >
                                        
                            </p>
                            <p class="supplier-number"><?= Html::encode($giftCard->gift_card_number) ?></p>
                            
                            <?php 

                             $giftCardSeller = GiftCardSeller::getSellerGiftCardInfoBySellerId($giftCard->gift_card_seller_id);
                            
                            ?>

                            <p><?= $giftCardSeller->name ?></p>
                          
                            <?php
                            $purchaseDate = $giftCard->purchased_date;
                            $newDate = date("d M Y", strtotime($purchaseDate));

                           
                        ?>
                            <p class="supplier-date"><i class="fas fa-calendar-alt"></i> <i><?=$newDate?></i></p>
                            <!-- <span class="supplier-tag">Tag 1</span><span class="supplier-tag">Another Tag</span> -->
                            &nbsp;
                            <hr>
                            <ul>
                                <li>
                                    <p>Price</p>
                                    <p class="supplier-price-value">$<?= number_format($giftCard->price, 2); ?></p>
                                </li>
                                &nbsp;
                                <li>
                                    <p>Balance</p>

                                    <?php

                                     $giftCardBalance = Transaction::getGiftCardBalanceByPurchasingAccount($giftCard->id);
                                    ?>
                                    <p class="supplier-price-value">$<?= number_format($giftCardBalance, 2); ?></p>
                                </li>
                                &nbsp; &nbsp;
                                <li>
                                    <p>Discount</p>
                                    <p class="supplier-price-value"><?= number_format($giftCard->discount_rate, 2)." %"; ?></p>
                                </li>
                            </ul>
                            &nbsp;&nbsp;
                            <div>
                            <span class="supplier-tag">Tag 1</span><span class="supplier-tag">Another Tag</span>

                            </div>
                           
                            <img src="<?= Yii::getAlias('@web/themes/espiralo') ?>/images/blank-image.png"  alt="Gift Card" class="img-fluid" />
                            
                        </div>
                    </div>
                   
                    <?php }?>
                </div>

                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <p>
                        Consequat occaecat ullamco amet non eiusmod nostrud dolore irure incididunt est duis
                        anim sunt officia. Fugiat velit proident aliquip nisi incididunt nostrud exercitation
                        proident est nisi. Irure magna elit commodo anim ex veniam culpa eiusmod id nostrud
                        sit cupidatat in veniam ad. Eiusmod consequat eu adipisicing minim anim aliquip
                        cupidatat culpa excepteur quis. Occaecat sit eu exercitation irure Lorem incididunt
                        nostrud.
                    </p>
                </div>
                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <p>
                        Consequat occaecat ullamco amet non eiusmod nostrud dolore irure incididunt est duis
                        anim sunt officia. Fugiat velit proident aliquip nisi incididunt nostrud exercitation
                        proident est nisi. Irure magna elit commodo anim ex veniam culpa eiusmod id nostrud
                        sit cupidatat in veniam ad. Eiusmod consequat eu adipisicing minim anim aliquip
                        cupidatat culpa excepteur quis. Occaecat sit eu exercitation irure Lorem incididunt
                        nostrud.
                    </p>
                </div>

                <!-- button area start -->
                <div class="row">
                 <div class="col-md-12 text-right pull-right" >

                    <div class="seller-btn" style="display: none" id="select-button">
                        <?= Html::a(''.' More...', Url::toRoute(['#']), ['class' => 'select-btn', 'id' => '']) ?>
                        <?= Html::a(''.' View Transaction', Url::toRoute(['/payment-method/payment-method-transaction/index']) , ['class' => 'select-btn', 'data-url' => Url::toRoute(['update']), 'id' => 'kanban-button-update']) ?>
                        <?= Html::a(''.' Charge', Url::toRoute(['/gift-card/gift-card-charge/create']), ['class' => 'select-btn', 'data-url' => '', 'id' => 'kanban-button-create-charge']) ?>
                        <?= Html::a(''.' Pay', Url::toRoute(['/gift-card/gift-card-payment/create']), ['class' => 'select-btn', 'data-url' => '', 'id' => 'kanban-button-create-payment']) ?>
                      
                        <?= Html::button('Reload', ['id' => 'kanban-button-create-reload', 'value' =>   Url::toRoute(['/gift-card/gift-card-reload/create']), 'class' => 'btn  seller-btn']) ?>
                    </div>

                    <div class="seller-btn" id="default-button" >
                        <?= Html::button('New Gift Card', ['id' => 'kanban-button-add', 'value' =>   Url::toRoute(['/gift-card/gift-card/create']), 'class' => 'btn  seller-btn']) ?>
                        <?= Html::a(''.' Customized Field', Url::toRoute(['#']), ['class' => 'select-btn', 'id' => '']) ?>
                        <?= Html::a(''.' View Transaction', Url::toRoute(['/payment-method/payment-method-transaction/index']) , ['class' => 'select-btn', 'data-url' => Url::toRoute(['update']), 'id' => 'kanban-button-update']) ?>
                        <?= Html::a(''.' More', Url::toRoute(['#']), ['class' => 'select-btn', 'id' => '']) ?>
                      
                    </div>
                  </div>
                </div>
                </div>
               

            </div>
            <!-- Tab Content End  -->
      
    </div>

     
    <?php

    // Gift Card Modal Popup

    Modal::begin([
        'header' => '',
        'id'     => 'inboxModal',
        'size'   => 'modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered',
        'closeButton' => [
            'id'=>'close-button-n',
            'class'=>'close',
            'data-dismiss' =>'modal',
        ],
    ]);

    echo "<div id='inbox-content-view'></div>";

    Modal::end();
    ?>
 
        <?php

        // Gift Card Modal Popup

        Modal::begin([
            'header' => '',
            'id'     => 'giftCardModal',
            'size'   => 'modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered',
            'closeButton' => [
                'id'=>'close-button-n',
                'class'=>'close',
                'data-dismiss' =>'modal',
            ],
        ]);

        echo "<div id='inbox-content-view'></div>";

        Modal::end();
        ?>

<script>
        $(document).on('click', '#kanban-button-add', function (event) {
       
            $('#giftCardModal').modal('show')
                .find('#inbox-content-view')
                .load($(this).attr('value'));
        });

        
        $(".giftcard").on('click', function (e) {


            var type = e.type;
            var docId = $(this).find('p').data('doc-id');
            var orderId = $(this).find('p').data('order-id');
         

            $('.giftcard').removeClass('active');
            $(this).addClass('active');
            $("#default-button").hide();
            $("#select-button").show();

            $("#kanban-button-create-reload").on('click', function (e) {

                var reloadButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card-reload/create'])?>?refDocId=' + docId;

               $('#inboxModal').modal('show')
                .find('#inbox-content-view')
                .load(reloadButtonUrl);

            });

            $("#kanban-button-create-payment").on('click', function (e) {

                var paymentButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card-payment/create'])?>?refDocId=' + docId;
                // $('#kanban-button-create-payment').attr('href', paymentButtonUrl);

                $('#inboxModal').modal('show')
                .find('#inbox-content-view')
                .load(paymentButtonUrl);

            });

            $("#kanban-button-create-charge").on('click', function (e) {

                var chargeButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card-charge/create'])?>?refDocId=' + docId;
                // $('#kanban-button-create-charge').attr('href', chargeButtonUrl);

                $('#inboxModal').modal('show')
                .find('#inbox-content-view')
                .load(chargeButtonUrl);

                });
               
        });

        $(document).on('dblclick', '.giftcard', function (event) {

            var docId = $(this).find('p').data('doc-id');

            var updateButtonUrl = '<?= Url::toRoute(['update'])?>?docId=' + docId;

            $('#inboxModal').modal('show')
            .find('#inbox-content-view')
            .load(updateButtonUrl);
            
        })



</script>

<?php
$js = <<< JS
 
            
JS;
$this->registerJs($js);
?>
    

