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
use yii\bootstrap\Widget;

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
    font-size: 14px;
    margin: 0 15px;
    cursor: pointer;
    text-transform: uppercase;
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

   body {
       overflow: hidden;
       height: 100%;
   }
   .scroll-box{
       height: calc(100vh - 260px);
       overflow-y: scroll;
       overflow-x: hidden;
   }

   .seller-btn{
       padding-top: 20px;
   }

   #pills-tab{
       margin-bottom: 0!important;
   }

   #main-content-body .supplier-item ul li {
        list-style: none;
        display: inline-block;
        margin-right: 5px;
    }

</style>

    <div class="main-container" role="main">
        <?php if(Yii::$app->session->hasFlash('error')):?>

            <div class=" alert alert-danger flash-error">
                <?= Yii::$app->session->getFlash('error') ?>
            </div>

        <?php endif; ?>

        <?php if(Yii::$app->session->hasFlash('success')):?>

            <div class=" alert alert-success flash-success">
                <?= Yii::$app->session->getFlash('success') ?>
            </div>

        <?php endif; ?>


            <div class="admin-grid-filterss">
                <?php echo $this->render('_search', ['model' => $searchModel]); ?>
            </div>

            <!-- Kanban View -->
            <div class="admin-grid-view admin-kanban-view">
                <?php
                $columns = 4;
                $cl = 12 / $columns;
                echo ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemOptions'  => ['class' => "col-sm-$cl"],
                    'itemView' => '_list',
                    'layout' => "{pager}\n{items}",
                    'viewParams' => [
                        'fullView' => true,
                        'context' => 'main-page',
                    ],
                    'beforeItem'   => function ($model, $key, $index, $widget) use ($columns) {
                        if ( $index % $columns == 0 ) {
                            return "<div class='row'>";
                        }
                    },
                    'afterItem' => function ($model, $key, $index, $widget) use ($columns) {
                        if (($index > 0) && ($index % $columns === $columns - 1)) {
                            return "</div>";
                        }
                    }
                ]);
                ?>
            </div>

            <!-- Tab Content Start  -->


                <!-- button area start -->
                <div class="row">
                    <div class="kanban-footer-button admin-form-button">
                        <div class="seller-btn" style="display: none" id="select-button">
                            <?= Html::a(''.' Deactivate', '#', ['class' => 'btn btn-info disable-item', 'data-url' => Url::toRoute(['update']), 'id' => 'kanban-button-deactive']) ?>
                            <?= Html::a(''.' Delete', '#', ['class' => 'btn btn-info disable-item' , 'data-method'=>'post', 'data-url' => Url::toRoute(['delete']), 'id' => 'kanban-button-delete']) ?>
                            <!-- <?= Html::a(''.' More...', Url::toRoute(['#']), ['class' => 'select-btn', 'id' => '']) ?> -->
                            <?= Html::a(''.'VIEW TRANSACTION', Url::toRoute(['/payment-method/payment-method-transaction/gift-card']), ['class' => 'select-btn', 'data-url' => '', 'id' => 'kanban-button-view-single-transaction']) ?>
                            <?= Html::button('Charge', ['id' => 'kanban-button-create-charge', 'value' =>   Url::toRoute(['/gift-card/gift-card-charge/create']), 'class' => 'btn  seller-btn']) ?>
                            <?= Html::button('Pay', ['id' => 'kanban-button-create-payment', 'value' =>   Url::toRoute(['/gift-card/gift-card-payment/create']), 'class' => 'btn  seller-btn']) ?>
                            <?= Html::button('Reload', ['id' => 'kanban-button-create-reload', 'value' =>   Url::toRoute(['/gift-card/gift-card-reload/create']), 'class' => 'btn  seller-btn']) ?>
                        </div>

                        <div class="seller-btn" id="default-button" >
                            <?= Html::button('Add New', ['id' => 'kanban-button-add', 'value' =>   Url::toRoute(['/gift-card/gift-card/create']), 'class' => 'btn  seller-btn']) ?>
                            <!-- <?= Html::a(''.' Customized Field', Url::toRoute(['#']), ['class' => 'select-btn', 'id' => '']) ?> -->
<!--                            --><?php //Html::a(''.' View Transaction', Url::toRoute(['/payment-method/payment-method-transaction/gift-card']) , ['class' => 'select-btn', 'data-url' => Url::toRoute(['update']), 'id' => 'kanban-button-update']) ?>
                            <!-- <?= Html::a(''.' More', Url::toRoute(['#']), ['class' => 'select-btn', 'id' => '']) ?> -->

                        </div>
                    </div>
                </div>

               

            </div>
            <!-- Tab Content End  -->
      
    </div>


    <?php

    // Gift Card Modal Popup

    Modal::begin([
        'header' => '<h4 class="modal-title dynamic-title" id="header-content"></h4>',
        'id'     => 'inboxModal',
        'size'   => 'modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered',
        'closeButton' => [
            'id'=>'close-button-n',
            'class'=>'modal-in-close-btn',
            'data-dismiss' =>'modal',
        ],
        'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
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
                'class'=>'modal-in-close-btn',
                'data-dismiss' =>'modal',
            ],
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
        ]);


        echo "<div id='inbox-content-view'></div>";

        Modal::end();

        Modal::begin([
            'header' => '<h4 class="modal-title dynamic-title" id="header-content"></h4>',
            'id'     => 'giftCardTransactionModalContent',
            'size'   => 'modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered',
            'closeButton' => [
                'id'=>'close-button-transaction',
                'class'=>'modal-in-close-btn',
                'data-dismiss' =>'modal',
            ],
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
        ]);

        echo "<div id='add-giftCardTransactionModalContent'></div>";

        Modal::end();
        ?>

<script>
    
    /* ------------------------------BEGIN Delete Toast ------------------------------ */
    $('a').off('click', 'a').click(function(e){
        var id = $(this).attr('id');
        var url = $(this).attr('href');

        if (id == 'kanban-button-delete')
        {
            e.preventDefault();
            e.stopPropagation();

            iziToast.show({
                buttons: [
                    ['<button class="mt-3 float-left btn"><b>YES</b></button>', function (instance, toast) {
            
                        instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');

                        $.ajax({
                            url: url,
                            type: "POST",
                            success: function(data){
                                // alert('data');
                            },
                            error: function(){
                                // alert('This record has relational data. You must delete the following data first.');
                            },
                            beforeSend: function(){
                            }
                        });

                    }, true],
                    ['<button class="mt-3 float-right btn">NO</button>', function (instance, toast) {
            
                        instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');
            
                    }],
                ]
            });
        }
    });
    /* ------------------------------END Delete Toast ------------------------------ */

        function reloadMainPopupAreaFunc() {

            var docId   = $('#w0 .active').find('p').data('doc-id');
            var orderId = $('#w0 .active').find('p').data('order-id');

            if(!docId && !orderId){
                if( $('div.modal-backdrop').length > 0 ){
                    $('div.modal-backdrop:last').remove();
                }

                $("#giftCardModal #inbox-content-view").html('');
                $("#giftCardModal #inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");

                var createButtonUrl = '<?=Url::toRoute(['/gift-card/gift-card/create'])?>';

                $('#giftCardModal').modal('show')
                    .find('#inbox-content-view')
                    .load(createButtonUrl);

            }else{

                if( $('div.modal-backdrop').length > 0 ){
                    $('div.modal-backdrop:last').remove();
                }

                $("#inbox-content-view").html('');
                $("#inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");

                var updateButtonUrl = '<?= Url::toRoute(['update'])?>?docId=' + docId;

                $('#inboxModal').modal('show')
                    .find('#inbox-content-view')
                    .load(updateButtonUrl);

            }

        }

        function reloadMainPopupDropdownAreaFunc(){

            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/get-gift-card-seller-list' ?>',
                type: 'POST',
                data: {
                    _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
                },
                success: function (data) {
                    $("#dep_drop_seller").html(data);
                    $("#dep_drop_seller option:last").attr("selected", "selected");

                    var dep_drop_seller_id = $("#dep_drop_seller").val();

                    $.ajax({
                        url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/seller-account-dep-drop-down-list' ?>',
                        type: 'POST',
                        data: {
                            _csrf: '<?=Yii::$app->request->getCsrfToken()?>',
                            sellerId: dep_drop_seller_id,
                            select2: 0
                        },
                        success: function (data) {
                            $("#gift_card_seller_account_id").removeAttr('disabled');
                            $("#gift_card_seller_account_id").html(data);
                        }
                    });

                }
            });

        }


        $(document).on('click', '#kanban-button-add', function (event) {

            $("#inbox-content-view").html('');
            $("#inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");

            $('#giftCardModal').modal('show')
                .find('#inbox-content-view')
                .load($(this).attr('value'));
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

        function reloadMainFilterFunc(){

            $("#inbox-content-view").html('');
            //location.reload();
            var form = $("#searchfilter");
            var formData = form.serializeArray();
            $.ajax({
                url: form.attr("action"),
                type: "POST",
                data: formData,
                dataType: 'html',
                success: function(data){
                    $(".list-view.scroll-box").html(data);
                }
            });

            $("#default-button").show();
            $("#select-button").hide();

        }


        // $(document).off('click', '#close-button-n').on('click', '#close-button-n', function (event) {
        //     reloadMainFilterFunc();
        // });

        //Refesh Kanban View
        // $('#inboxModal').on('hidden.bs.modal', function () {
        //     reloadMainFilterFunc();
        // });

        $("button[data-dismiss=modal]").off("click", "button[data-dismiss=modal]").click(function(){
            reloadMainFilterFunc();
        });

        //FadeOut Session Message

        $(".flash-success").delay(3000).fadeOut("slow");
        $(".flash-error").delay(3000).fadeOut("slow");

        $('#close-button-n').off('click', '#close-button-n').click(function(){
            $('#giftCardModal').modal('hide');
            reloadGiftCardWebIndex();
        });


</script>

<?php
$js = <<< JS
 
            
JS;
$this->registerJs($js);
?>
    

