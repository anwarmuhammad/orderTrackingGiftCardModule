<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\core\buttons\FormButtons;
use unlock\modules\core\buttons\BackButton;
use unlock\modules\core\grid\GridView;
use unlock\modules\core\widgets\LinkPager;
use unlock\modules\purchaseorder\models\PurchaseOrder;
use unlock\modules\supplier\models\Supplier;
use unlock\modules\purchaseorder\models\PurchaseOrderDocItem;
use yii\helpers\ArrayHelper;
use unlock\modules\core\helpers\CostCalculation;
use unlock\modules\purchasingaccount\models\PurchasingAccount;
use unlock\modules\cashback\models\CashBackWebsite;
use unlock\modules\purchaseorder\models\PurchaseOrderPayment;
use unlock\modules\purchaseorder\models\PurchaseOrderInvoice;
use unlock\modules\category\models\Category;
use unlock\modules\paymentmethod\models\TransactionPaymentMethod;
use unlock\modules\supplier\models\SupplierAccountSearch;
use unlock\modules\supplier\models\GiftCardSeller;
use unlock\modules\giftcard\models\GiftCard;

/* @var $this yii\web\View */
/* @var $model unlock\modules\purchaseorder\models\PurchaseOrder */
/* @var $form yii\bootstrap\ActiveForm */



$isNewRecord = $model->isNewRecord;

$this->title = ($model->isNewRecord) ? Yii::t('app', 'Create Gift Card Payment') : Yii::t('app', 'Update Gift Card Payment');
?>

<style>
    .field-purchaseorderpayment-payment_date .help-block {
        display: none;
    }
    /* scrollbar design */
    /* width */
    ::-webkit-scrollbar {
        width: 5px;
        right: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        box-shadow: none;
        border-radius: 10px;
    }
    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #E3E3E3;
        border-radius: 10px;
    }
    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #dfdfdf;
    }
    .modal-body{
        padding-bottom: 0;
    }
    .modal-header button.close{
        right: 69px!important;
    }
    .modal-dialog-centered.modal-dialog-scrollable .modal-content{
        height: 100%!important;
    }
    .modal-header{
        border: none!important;
        margin-left: 86px;
        padding-bottom: 0;
    }

    .admin-form-button {
        float: none;
        display: block;
        background: transparent!important;
        width: 93%;
        text-align: right;
        margin: 0 auto;
        padding-top: 20px;
    }

    .btn-add-more{
        position: relative;
        top: -15px;
        left: -12px;
    }

    .box-content table td{
        padding: 0;
    }

    .control-label {
        margin-bottom: 0;
        color: #888888;
        font-family: 'Kanit-Light';
        font-size: 14px!important;
    }

    .form-control{
        color: #888888;
    }

    .modal-header{
        border: none!important;
        margin-left: 86px;
        padding-bottom: 0;
    }
    #inbox-content-view .modal-body{
        padding-top: 0;
    }
    .remove-payment-method-row{
        background: none!important;
    }

    .modal-content{
        height: 100%!important;
    }
    .g-pay-date label{
        margin-right: 6px;
        float: left;
        margin-top: -1px;
    }
    .g-pay-date .field-giftcardpayment-gift_card_pay_date{
        width: 165px;
    }
    .g-pay-date .input-group-datepicker{
        width: 92px!important;
    }


    #box-payment-method-two .table td, .table th {
        vertical-align: unset;
        border-top: none;
    }
    #box-payment-method-two .table label{
        font-size: 20px;
    }
    #box-payment-method-two .table label span{
        font-size: 20px;
    }


    #box-payment-method-two .box-content select,
    #box-payment-method-two .box-content input{
        height: 30px;
        color:#888888!important;
        font-family: 'Kanit-Medium';
    }
    #box-payment-method-two .box-content input.field-debit-amount, #box-payment-method-two .box-content input.field-debit-amount-refund{
        padding-left: 16px;
    }
    #box-payment-method-two .box-content input.field-cash-back-rate {
        padding-left: 10px;
    }
    .add-another-btn a,
    .add-another-btn svg{
        color: #888888;
    }
    #box-payment-method{
        padding: 0;
        border: none;
        padding-right: 35px;
    }
    .dynamic-multiple-account-table td{
        padding-bottom: 10px!important;
        padding-right: 10px!important;
    }
    .dynamic-content-table td {
        padding-bottom: 10px!important;
        padding-right: 35px!important;
    }
    #box-payment-method table tr td{
        padding-bottom: 10px!important;
        padding-right: 10px!important;
    }
    .select2-container--krajee .select2-selection--single .select2-selection__arrow {
        border: none;
        position: absolute;
        height: 32px;
        top: 3px;
        right: 0px!important;
        width: 20px;
    }
    .select2-container--krajee {
        width: 100%!important;
        max-width: 100%!important;
    }
    .payment-method-cash-back-value .input-box-percent-sign {
        position: absolute;
        top: 1px;
        left: 60px;
    }
    .balance-reload{
        display: inline-block;
        position: absolute;
        right: -100px;
        top: 25px;
        color: #888888;
    }
    .singleAmount-total .amount-icon {
        left: 3px;
        top: 5px;
    }
    select#dep_drop_seller {
        width: 100%!important;
        height: 100%!important;
        visibility: unset!important;
    }
    .psub2-total .amount-icon {
        position: absolute;
        left: 8px;
        top: 3px;
        font-size: 1rem;
        color: #888;
    }

    .table-no-bordered .remove-payment-method-row svg {
        position: relative;
        left: 5px;
        color: #fff!important;
        top: 28px;
    }
</style>
<div class="main-container main-container-form" role="main">

    <div class="admin-form-container">
        <div class="panel panel-default">

            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',  //  'default', 'horizontal' or 'inline'
                    'id' => 'gift-card-reload',
                    /*'options' => ['enctype' => 'multipart/form-data'],*/
                    'validateOnBlur' => true,
                    'enableAjaxValidation' => false,
                    'errorCssClass' => 'has-error',
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"\">\n{input}\n{hint}\n{error}\n</div>",
                        'options' => ['class' => 'form-group'],
                        'horizontalCssClasses' => [
                            'label' => '',
                            'offset' => '',
                            'wrapper' => '',
                            'hint' => '',
                        ],
                    ],
                ]); ?>

                <div class="panel-box panel-box-bg" id="order-form-header"  style="padding: 3px 0px 0px 0px">
                    <div class="box-content">
                        <div class="row">
                            <div class="col-sm-3" style="display:none;">
                                <?php if(!$isNewRecord) { ?>
                                    <p style="padding: 0px 0px 0px 24px"><?= $form->field($model, 'doc_id', ['template' => $model->doc_id, 'options' => ['tag' => false] ])->hiddenInput() ?> </p>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="box-payment-method-two" style="overflow: hidden; height: calc(100vh - 290px); border: 1px solid #8c368c; margin: 0 15px; padding: 20px; border-radius: 3px; margin-bottom: 20px; margin: 0 auto; width: 93%;">
                    <div class="row" style="overflow-y: auto; max-height: calc(100vh - 290px); position: relative;">
                        <div class="g-pay-date" style="position: absolute; right: 0;">
                            <?= $form->field($model, 'gift_card_reload_date', ['template' => Yii::$app->getModule('core')->datePickerTemplate()])->widget(\yii\jui\DatePicker::className(),
                                [
                                    'options' => ['class' => 'form-control'],
                                    'language' => 'en-GB',
                                    'value' => $model->gift_card_reload_date,
                                    'dateFormat' => 'MM/dd/yyyy',
                                    'clientOptions' => [
                                        'changeYear' => true,
                                        'changeMonth' => true,
                                        'todayHighlight' => true,
                                    ],
                                ]) ?>
                        </div>
                        <div class="col-sm-8 col-sm-offset-4">
                            <div class="panel-box panel-box-border" id="box-gift-card">
                                <div class="box-content">
                                    <table class="table table-no-bordered dynamic-content-table">

                                        <tr>
                                            <td>
                                                <?= $form->field($model, 'gift_card_id')->dropDownList(GiftCard::giftCardDropDownList2($model['gift_card_id']),['prompt'=>'Select '.$model->getAttributeLabel('gift_card_id'),'class' => 'form-control payment-method-gift-card-number'])->label("Gift Card") ?>

                                                <div class="balance-reload">
                                                    Balance &ensp;<span id='gift-card-balance'>
                                                     <?php if($model->gift_card_id) {
                                                         echo $giftCardBalance ;
                                                     } ?>
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $form->field($model, 'seller_id')->widget(Select2::classname(), [
                                                    'data' => GiftCardSeller::giftCardSellerDropDownList2($model['seller_id']),
                                                    'language' => 'en-GB',
                                                    'options' => ['options' => GiftCardSeller::giftCardSellerOptionsDropDownList(), 'id' => 'dep_drop_seller', 'placeholder' => 'Select ' . $model->getAttributeLabel('seller_id')],
                                                    'pluginOptions' => [
                                                        'allowClear' => true
                                                    ],
                                                ]);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?php
                                                if (empty($model->seller_account_id)) {
                                                    echo $form->field($model, 'seller_account_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                                                        'options' => ['id' => 'seller_account_id'],
                                                        'pluginOptions' => [
                                                            'depends' => ['dep_drop_seller'],
                                                            'initialize' => true,
                                                            'placeholder' => 'Select ' . $model->getAttributeLabel('seller_account_id'),
                                                            'url' => Url::to(['/ajax/ajax/seller-account-dep-drop-down-list'])
                                                        ]
                                                    ]);
                                                } else {
                                                    echo $form->field($model, 'seller_account_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                                                        'data' => [$model->seller_account_id => 'default'],
                                                        'options' => ['id' => 'seller_account_id'],
                                                        'pluginOptions' => [
                                                            'depends' => ['dep_drop_seller'],
                                                            'initialize' => true,
                                                            'placeholder' => 'Select ' . $model->getAttributeLabel('seller_account_id'),
                                                            'url' => Url::to(['/ajax/ajax/seller-account-dep-drop-down-list'])
                                                        ]
                                                    ]);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $form->field($model,  'price', ['template' => '{label}<div class="nopadding"><div class="psub2-total"><span class="amount-icon">$</span>{input}{error}</div></div>'])->textInput() ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $form->field($model,  'value', ['template' => '{label}<div class="nopadding"><div class="psub2-total"><span class="amount-icon">$</span>{input}{error}</div></div>'])->textInput() ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $form->field($model,  'discount_rate')->textInput() ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $form->field($model, 'cash_back_website_id')->dropDownList(CashBackWebsite::cashBackWebsiteDropDownList2($model['cash_back_website_id']),['prompt'=>'Select '.$model->getAttributeLabel('cash_back_website_id')])->label("Website Cashback") ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $form->field($model,  'cash_back_website_rate')->label('Website Cashback %') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $form->field($model,  'note')->label('Note') ?>
                                            </td>
                                        </tr>


                                    </table>

                                    <div class="row" style="padding-bottom: 15px">
                                        <div class="col-sm-12">
                                            <div class="panel-box panel-box-border" id="box-payment-method">
                                                <div class="box-title" style="padding: 0;">
                                                    <h3>Payment Methods</h3>
                                                </div>
                                                <div class="box-content">
                                                    <table class="table table-no-bordered">
                                                        <?php foreach ($modelPaymentMethodItems as $paymentIndex => $paymentItem){ ?>
                                                            <?php if($hasPaymentMethodItem) { ?>
                                                                <tr style="width:100%">
                                                                    <td style="width:20%">
                                                                        <div class="paymentDoc-label">Type<span class="text-danger">*</span></div>

                                                                        <?= $form->field($paymentItem, "[$paymentIndex]id", ['template' => '{input}', 'options' => ['tag' => null]])->hiddenInput(['class' => 'field-id']); ?>
                                                                        <?= $form->field($paymentItem, "[$paymentIndex]purchasing_account_type", ['template' => '{input}'])->dropDownList(CommonHelper::getAccountTypePaymentDropDownList(), ['prompt' => 'Select Account Type', 'class' => 'form-control payment-method-account-type']) ?>
                                                                    </td>
                                                                    <td style="width:20%">
                                                                        <div class="paymentDoc-label">Account<span class="text-danger">*</span></div>

                                                                        <?php
                                                                        $purchasingAccount = PurchasingAccount::purchasingAccountDropDownList($paymentItem['purchasing_account_type'], $paymentItem['purchasing_account_id'],true);
                                                                        $purchasingAccount['items'] = !empty($purchasingAccount['items']) ? $purchasingAccount['items'] : [];
                                                                        $purchasingAccount['options'] = !empty($purchasingAccount['options']) ? $purchasingAccount['options'] : [];
                                                                        echo $form->field($paymentItem, "[$paymentIndex]purchasing_account_id", ['template' => '{input}'])->dropDownList($purchasingAccount['items'], ['prompt' => 'Select Card Number','options' => $purchasingAccount['options'], 'class' => 'form-control payment-method-account'])
                                                                        ?>
                                                                    </td>
                                                                    <!-- <td style="width:7%">
                                                                        Amount
                                                                    </td> -->
                                                                    <td style="width:20%">
                                                                        <div class="paymentDoc-label">Amount<span class="text-danger">*</span></div>

                                                                        <?= $form->field($paymentItem, "[$paymentIndex]spent_amount", ['template' => '{input}'])->textInput(['class' => 'form-control field-debit-amount']); ?>
                                                                    </td>
                                                                    <!-- <td class="payment-method-cash-back-label hide" style="width:8%">
                                                                        Cashback
                                                                    </td> -->
                                                                    <td class="payment-method-cash-back-value hide" style="width:20%">
                                                                        <div class="paymentDoc-label">Cashback</div>

                                                                        <?= $form->field($paymentItem, "[$paymentIndex]cash_back_rate", ['template' => '{input}<span class="input-box-percent-sign">%</span>'])->textInput(['class' => 'form-control field-cash-back-rate']); ?>
                                                                    </td>
                                                                    <td  style="width:5%">
                                                                        <a href="javascript:void(0)" class="remove-payment-method-row"><svg xmlns="http://www.w3.org/2000/svg" width="16.123" height="16.123" viewBox="0 0 16.123 16.123"><g transform="translate(-825.439 -620.439)"><circle cx="8.061" cy="8.061" r="8.061" transform="translate(825.439 620.439)" fill="#b2b2b2"/><g transform="translate(829.681 624.681)"><line x2="7.565" y2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/><line y1="7.565" x2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/></g></g></svg></a>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        <tr>
                                                            <td colspan="5" height="10"></td>
                                                        </tr>
                                                    </table>
                                                    <div class="add-another-btn" style="padding-bottom: 10px;">
                                                        <a href="javascript:void(0)" class="btn btn-add-more add-new-payment-row" id="add-new-payment-row">
                                                            <i class="fa fa-plus-circle"></i> &nbsp;Add Payment Method
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <label>Total: <span id="sum-payment-amount" data-color="">$0</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="admin-form-button">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <p id="form-submit-error-message" style="color:red;"></p>
                        </div>

                        <div class="col-sm-12">
                            <input id="delete-gift-card-payment-item-ids" name="delete-gift-card-payment-item-ids" type="hidden">
                            <input id="delete-payment-method-ids" name="delete-payment-method-ids" type="hidden">
                            <?= Html::a('<i class=""></i> ' . Yii::t('app', 'Delete'),Url::toRoute(['delete', 'docId' => $model->doc_id]), ['class' => 'btn btn-warning return_margin _delete disable-item', 'data-method'=>'post', 'data-url' => Url::toRoute(['delete', 'docId' => $model->doc_id]), 'id' => 'kanban-button-delete']) ?>
                            <?= FormButtons::widget(['item' => ['save'], 'docId' => $model->doc_id]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


<div class="hidden-field-for-javascript" style="display:none">
    <?= Html::dropDownList('purchasing_account_type_hidden_field', null, CommonHelper::getAccountTypePaymentDropDownList(), ['prompt' => 'Select Account Type', 'id' => 'hidden-drop-down-field-payment-method-account-type']) ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        calculateGiftCardDiscount();
        // Payment Method
        $(document).on("change", ".payment-method-account-type", function () {
            var paymentMethodCardType = $(this).val();
            var parentThis = $(this);
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/payment-method' ?>',
                type: 'POST',
                data: {
                    paymentMethodCardType: paymentMethodCardType,
                    _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
                },
                success: function (data) {
                    parentThis.closest('tr').find('.payment-method-account').prop("disabled", false);
                    parentThis.closest('tr').find('.payment-method-account').html(data);
                }
            });
        });

        // Price Selection w.r.t Card Number
        $(document).on("change", ".gift-card-number", function () {
            var giftCardPrice = $(this).find(':selected').data('price');
            $(this).closest('tr').find('.field-amount').val(giftCardPrice);
        });
        $(".gift-card-number").trigger("change");

        $(document).on("change", ".payment-method-account", function () {

            var purchasingAccount = $(this).val();
            var paymentMethodCardType = $('#giftcardpayment-purchasing_account_type').val();
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/balance' ?>',
                type: 'POST',
                data: {
                    accountNumber: purchasingAccount,
                    accountType: paymentMethodCardType,
                    _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
                },
                success: function (data) {
                    $("#balance").text('$' + data);
                }
            });
        });


        $("#giftcardreload-gift_card_id").change(function () {

            var paymentAccountType  = "gift-card";
            var paymentAccountId    = $(this).val();

            loadAccountBalance(paymentAccountType, paymentAccountId);
        });
        function loadAccountBalance(paymentAccountType, paymentAccountId) {
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/load-payment-account-balance' ?>',
                type: 'POST',
                data: {
                    paymentAccountType: paymentAccountType,
                    paymentAccountId: paymentAccountId,
                    _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
                },
                success: function (data) {
                    $("#gift-card-balance").html('$' + data);
                }
            });
        }

        $(document).on('keyup', '#giftcardreload-price, #giftcardreload-value', function () {
            calculateGiftCardDiscount();
        });

        function calculateGiftCardDiscount() {

            var price = parseFloat($('#giftcardreload-price').val());
            var value = parseFloat($('#giftcardreload-value').val());

            if(value && price ){

                var discount = (100*(value-price))/value
            }

            if(!isNaN(discount) && discount >=0){
                $("#giftcardreload-discount_rate").val(discount.toFixed(2));
            }

        }
        //Default CashBack Website and % w.r.t Seller

        $(document).off().on("change", "#dep_drop_seller", function (event) {
            event.preventDefault();
            event.stopPropagation();

            var sellerId = $(this).val();

            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/default-cashback-percentage' ?>',
                type: 'POST',
                data: {
                    sellerId: sellerId,
                    _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
                },
                success: function (data) {

                    $("#giftcardpayment-cash_back_website_id").val(data.cash_back_website_id);
                    $("#giftcardpayment-cash_back_website_rate").val(data.cash_back_website_rate);
                }
            });

        });




        //Form Validation
        $('#gift-card-reload').on('beforeValidate', function (event) {
            var paymentTotal = 0;

            $('#box-payment-method .box-content tbody tr').each(function () {
                paymentTotal += parseFloat($(this).find('.field-debit-amount').val()) || 0;
            });

            if (!isNaN(paymentTotal)) {
                $("#sum-payment-amount").text(paymentTotal.toFixed(2)).css('color','black');
                $(".payment-mismitch").css('color','black');
            }

            var giftCardPrice = parseFloat($('#giftcardreload-price').val()) || 0;

            if(paymentTotal != giftCardPrice){
                $('#form-submit-error-message').html('Total Pay Amount must be equal to Gift Card Price ');
                return false;
            }
        });


        $('#gift-card-reload').on('beforeSubmit', function(event) {
            event.stopImmediatePropagation();
            var purchaseForm = $("#gift-card-reload");
            var formData = purchaseForm.serializeArray();

            $.ajax({

                url: purchaseForm.attr("action"),
                //type: form.attr("method"),
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    var obj = jQuery.parseJSON( JSON.stringify(data) );
                    if(data == "1"){

                        $("#form-submit-error-message").html("Successfully Saved Data.");
                        $("#form-submit-error-message").attr("style", "color:green");
                        setTimeout(function(){// wait for 5 secs(2)
                            //location.reload(); // then reload the page.(3)
                            reloadMainPopupAreaFunc();
                        }, 1000)
                    }else{
                        $.each( obj, function( key, value ) {
                            $("#form-submit-error-message").html(value);
                            $("#form-submit-error-message").attr("style", "color:red");
                        });
                    }
                    setTimeout(function(){
                        $("#form-submit-error-message").attr("style", "display:none");
                    }, 5000);

                },
                error: function () {
                    // alert("Something went wrong For Save Form")
                },
            });

        }).on('submit', function(e){

            e.preventDefault();

        });
    })

</script>

<script type="text/javascript">
    $(document).ready(function () {

        var deleteGiftCardPaymentItemIds = [];

        // Add New Row
        var index = $('#dynamic-multiple-account tbody tr').length;

        $(document).on('click', '.remove-account-row', function () {
            var confirmResult = confirm("Are you sure you want to delete this item?");
            if (confirmResult) {
                var accountId = $(this).closest("tr").find('.field-gift-card-payment-id').val();
                if (typeof accountId != "undefined" && accountId != "" && accountId != null) {
                    deleteGiftCardPaymentItemIds.push(accountId);
                    $('#delete-gift-card-payment-item-ids').val(deleteGiftCardPaymentItemIds);
                }

                $(this).closest("tr").remove();
            }
        });


        // Add Dynamic Row
        $('.btn-add-new-account-row').on('click', function () {
            var rowHtmlPayment = prepareRow(index);
            $('#dynamic-multiple-account tbody').append(rowHtmlPayment);
            index++;

        })
    });

    function prepareRow(index) {
        var html = '';

        html += '<tr>';

        // Account Text Box
        var giftCardNumberList = $('#hidden-drop-down-field-gift-card-number').html();

        // Card Number
        html += '<td>' +
            '<div class="form-group field-giftcardpaymentitem-' + index + '-gift_card_id">' +
            '<select id="giftcardpaymentitem-' + index + '-gift_card_id" class="form-control dynamic-field-validation gift-card-number" name="GiftCardPaymentItem[' + index + '][gift_card_id]">' + giftCardNumberList + '</select>' +
            '</div>' +
            '</td>';

        // Amount Text Box
        html += '<td>' +
            '<div class="form-group field-giftcardpaymentitem-' + index + '-amount">' +
            '<input type="text" id="giftcardpaymentitem-' + index + '-amount" class="form-control dynamic-field-validation field-amount" name="GiftCardPaymentItem[' + index + '][amount]" aria-invalid="true">' +
            '</div>' +
            '</td>';

        // Delete Button
        html += '<td>' +
            '<a href="javascript:void(0)" class="remove-account-row"><svg xmlns="http://www.w3.org/2000/svg" width="16.123" height="16.123" viewBox="0 0 16.123 16.123"><g transform="translate(-825.439 -620.439)"><circle cx="8.061" cy="8.061" r="8.061" transform="translate(825.439 620.439)" fill="#b2b2b2"/><g transform="translate(829.681 624.681)"><line x2="7.565" y2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/><line y1="7.565" x2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/></g></g></svg></a>' +
            '</td>';

        html += '</tr>';

        return html;
    }

    function addDynamicValidation(index, fieldName, errorMessage) {
        $('#formName').yiiActiveForm('add', {
            id: 'giftcardpaymentitem-' + index + '-' + fieldName,
            name: '[' + index + ']' + fieldName,
            container: '.field-giftcardpaymentitem-' + index + '-' + fieldName,
            input: '#giftcardpaymentitem-' + index + '-' + fieldName,
            error: '.help-block.help-block-error',
            validate: function (attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {message: errorMessage});
            }
        });
    }
</script>

<script type="text/javascript">
    $(document).ready(function ()
    {
        // Add New Row
        var deleteOrderPaymentMethodIds = [];
        var indexPaymentMethod = $('#box-payment-method .box-content tbody tr').length;

        // Remove Row
        $(document).on('click', '.remove-payment-method-row', function () {
            var confirmResult = confirm("Are you sure you want to delete this item?");
            if (confirmResult) {
                var paymentMethodId = $(this).closest("tr").find('.field-id').val();
                if (typeof paymentMethodId != "undefined" && paymentMethodId != "" && paymentMethodId != null) {
                    deleteOrderPaymentMethodIds.push(paymentMethodId);
                    $('#delete-payment-method-ids').val(deleteOrderPaymentMethodIds);
                }

                $(this).closest("tr").remove();
            }
        });

        // Add Dynamic Row
        $('.add-new-payment-row').on('click', function ()
        {
            var rowHtmlPayment = preparePaymentMethodRow(indexPaymentMethod);
            $('#box-payment-method .box-content tbody').append(rowHtmlPayment);
            indexPaymentMethod++;
        });

        showHideCashBackField();
        $(document).on("change", ".payment-method-account-type", function() {
            var paymentMethodCardType = $(this).val();
            var parentThis = $(this);
            showHideCashBackField();
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl. '/ajax/ajax/payment-method' ?>',
                type: 'POST',
                data:{
                    paymentMethodCardType: paymentMethodCardType,
                    _csrf : '<?=Yii::$app->request->getCsrfToken()?>'
                },
                success: function (data) {
                    parentThis.closest('tr').find('.payment-method-account').prop("disabled", false);
                    parentThis.closest('tr').find('.payment-method-account').html(data);
                }
            });
        });

        $(document).on("change", ".payment-method-account", function () {
            var paymentMethodCashBackRate = $(this).find(':selected').data('cash-back-rate');
            $(this).closest('tr').find('.field-cash-back-rate').val(paymentMethodCashBackRate);
        });

        // Calculate Payment Total
        calculatePaymentTotal();
        $(document).on('blur', '.field-debit-amount, .field-cash-back-rate', function () {
            calculatePaymentTotal();
        });

        // Calculate Price Total
        calculatePriceTotal();

        $(document).on('change', '#box-gift-card .box-content tbody tr', function () {

            calculatePriceTotal();
        });

    });

    function preparePaymentMethodRow(indexPayment) {
        var html = '';
        
        html += '<tr style="width:100%">';

        // Account Type
        var paymentMethodTypeList = $('#hidden-drop-down-field-payment-method-account-type').html();


        html += '<td style="width:20%">' +
            '<div class="paymentDoc-label">Type<span class="text-danger">*</span></div>'+
            '<div class="form-group field-transaction-' + indexPayment + '-purchasing_account_type">' +
            '<select id="transaction-' + indexPayment + '-purchasing_account_type" class="form-control payment-method-account-type" name="Transaction[' + indexPayment + '][purchasing_account_type]">'+paymentMethodTypeList+'</select>' +
            '</div>' +
            '</td>';

        // Card Number
        html += '<td style="width:20%">' +
            '<div class="paymentDoc-label">Account<span class="text-danger">*</span></div>'+
            '<div class="form-group field-transaction-' + indexPayment + '-purchasing_account_id has-success">' +
            '<select id="transaction-' + indexPayment + '-purchasing_account_id" class="form-control payment-method-account" name="Transaction[' + indexPayment + '][purchasing_account_id]" aria-invalid="false" disabled="disabled">' +
            '<option value="">Select Card Number</option>' +
            '</select>' +
            '</div>' +
            '</td>';

        // Amount Text
        // html += '<td style="width:7%">' +
        //     'Amount' +
        //     '</td>';

        // Amount Text Box
        html += '<td style="width:20%">' +
            '<div class="paymentDoc-label">Amount<span class="text-danger">*</span></div>'+
            '<div class="form-group field-transaction-' + indexPayment + '-spent_amount">' +
            '<input type="text" id="transaction-' + indexPayment + '-spent_amount" class="form-control field-debit-amount" name="Transaction[' + indexPayment + '][spent_amount]" aria-invalid="true">' +
            '</div>' +
            '</td>';

        // Cashback Field
        // html += '<td class="payment-method-cash-back-label hide" style="width:8%">' +
        //     'Cashback' +
        //     '</td>';

        // Cashback Amount
        html += '<td class="payment-method-cash-back-value hide" style="width:20%">' +
            '<div class="paymentDoc-label">Cashback</div>'+
            '<div class="form-group field-transaction-' + indexPayment + '-cash_back_rate">' +
            '<input type="text" id="transaction-' + indexPayment + '-cash_back_rate" class="form-control dynamic-field-validation field-cash-back-rate" name="Transaction[' + indexPayment + '][cash_back_rate]"  aria-invalid="false">' +
            '<span class="input-box-percent-sign">%</span>' +
            '</div>' +
            '</td>';

        // Delete Button
        html += '<td style="width:5%">' +
            '<a href="javascript:void(0)" class="remove-payment-method-row"><svg xmlns="http://www.w3.org/2000/svg" width="16.123" height="16.123" viewBox="0 0 16.123 16.123"><g transform="translate(-825.439 -620.439)"><circle cx="8.061" cy="8.061" r="8.061" transform="translate(825.439 620.439)" fill="#b2b2b2"/><g transform="translate(829.681 624.681)"><line x2="7.565" y2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/><line y1="7.565" x2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/></g></g></svg></a>' +
            '</td>';

        html += '</tr>';

        return html;
    }

    function addDynamicValidation(index, fieldName, errorMessage){
        $('#formName').yiiActiveForm('add', {
            id: 'transaction-'+index+'-'+fieldName,
            name: '['+index+']'+fieldName,
            container: '.field-transaction-'+index+'-'+fieldName,
            input: '#transaction-'+index+'-'+fieldName,
            error: '.help-block.help-block-error',
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {message: errorMessage});
            }
        });
    }

    function showHideCashBackField() {
        $('#box-payment-method tbody tr').each(function () {
            var cardType = $(this).find('.payment-method-account-type').val();
            if (cardType == 'credit-card') {
                $(this).closest('tr').find('.payment-method-cash-back-label').removeClass('hide');
                $(this).closest('tr').find('.payment-method-cash-back-value').removeClass('hide');
            }
            else {
                $(this).closest('tr').find('.payment-method-cash-back-label').addClass('hide');
                $(this).closest('tr').find('.payment-method-cash-back-value').addClass('hide');
            }
        });
    }

    function calculatePaymentTotal() {
        var paymentTotal = 0;

        $('#box-payment-method .box-content tbody tr').each(function () {
            paymentTotal += parseFloat($(this).find('.field-debit-amount').val()) || 0;
        });

        if (!isNaN(paymentTotal)) {
            $("#sum-payment-amount").text(paymentTotal.toFixed(2)).css('color','black');
            $(".payment-mismitch").css('color','black');
        }

        var giftCardPrice = parseFloat($('#giftcardreload-price').val()) || 0;

        if(paymentTotal != giftCardPrice){
            $('#sum-payment-amount').html(paymentTotal.toFixed(2) + 'Total Pay Amount must be equal to Gift Card Price').css('color','red');
            $(".payment-mismitch").css('color','red');
            return false;
        }
    }

    //Calculate Total Price

    function calculatePriceTotal() {

        var priceTotal = 0;

        $('#box-gift-card .box-content tbody tr').each(function () {

            priceTotal += parseFloat($(this).find('.field-amount').val()) || 0;

        });

        if (priceTotal>0) {

            $("#giftcardpayment-price").val(priceTotal).css('color','black');
        }
    }

    $('._delete').off('click', '._delete').click(function(e) {

        e.preventDefault();
        e.stopImmediatePropagation();

        var url = $(this).attr("href");

        iziToast.show({
            buttons: [
                ['<button class="mt-3 float-left btn"><b>YES</b></button>', function (instance, toast) {

                    instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');

                    if(url){
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
                            },
                            success: function (data) {}
                        });
                    }
                }, true],
                ['<button class="mt-3 float-right btn">NO</button>', function (instance, toast) {

                    instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');

                }],
            ]
        });
    });
</script>
