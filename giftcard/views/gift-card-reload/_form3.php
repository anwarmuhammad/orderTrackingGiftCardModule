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

$this->title = ($model->isNewRecord) ? Yii::t('app', 'Create Gift Card Reload') : Yii::t('app', 'Update Gift Card Reload');
?>

<style>
    .field-purchaseorderpayment-payment_date .help-block {
    display: none;
    }

     .modal-header {
        padding: 1rem 0rem;
        border-bottom: 0px solid #fff!important;
        /* background-color: #3C4B48; */
    }

    .footer-total {
    border: 1px solid #8c368c;
    padding: 25px 32px;
    border-radius: 5px;
   }

   .gift-card-content{
        margin-bottom: 10px;
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
        top: 7px;
        left: -10px;
    }
    .control-label{
        margin-top: 10px!important;
    }
    .field-giftcardreload-gift_card_id .control-label{
        margin-top: 0!important;
    }
    .field-giftcardreload-gift_card_id{
        position: relative;
        width: 85%;
    }
    .balance-reload{
        display: inline-block;
        position: absolute;
        right: 0;
        top: 25px;
    }

</style>  
<div class="main-container main-container-form" role="main">
    <div class="page-header">
       
        <div class="page-header-breadcrumb">
           
        </div>
    </div>

    <div class="admin-form-container">
        <div class="panel panel-default">
        <div id="box-payment-method">
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',  //  'default', 'horizontal' or 'inline'
                    'id' => 'gift-card-reload',
                    /*'options' => ['enctype' => 'multipart/form-data'],*/
                    'validateOnBlur' => true,
                    'enableAjaxValidation' => true,
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
                
                <!-- <div class="panel-box panel-box-bg" id="order-form-header"  style="padding: 3px 0px 0px 0px"> -->
               
                <div class="modal-body">
                        <div class="row" style="display: none;">
                        <div  class="col-md-3 input-group form-control">

                            <p>Reload Gift Card</p>
                            <?php if(!$isNewRecord) { ?>
                                <?= $form->field($model, 'doc_id', ['inputTemplate' => $model->doc_id])->hiddenInput()->label(false) ?>
                                    <?php } ?>
                        </div>


                        </div>
                          
                    </div>
                </div>

                <div style="overflow: hidden; height: calc(100vh - 283px); border: 1px solid #8c368c; margin: 0 15px; padding: 20px; border-radius: 3px; margin-bottom: 20px; margin: 0 auto; width: 93%;">

                <div class="row" style="overflow-y: auto; max-height: calc(100vh - 283px); position: relative;">
                    <div class="g-pay-date" style="position: absolute; right: 13px; top: 6px;">
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
                            ])->label(false) ?>
                    </div>
                    <div class="col-sm-8 col-sm-offset-4">
                        <div class="panel-box panel-box-border" id="box-gift-card-method box-gift-card">
                            <div class="box-content">
                                   
                                     
                                            <?= $form->field($model, 'gift_card_id')->dropDownList(GiftCard::giftCardDropDownList(),['prompt'=>'Select '.$model->getAttributeLabel('gift_card_id'),'class' => 'form-control payment-method-gift-card-number'])->label("Gift Card") ?>


                                            <div class="balance-reload">
                                                Balance &ensp;<span id='gift-card-balance'>
                                                     <?php if($model->gift_card_id) {
                                                         echo '$'.$giftCardBalance ;
                                                     } ?>
                                                    </span>
                                            </div>
                                       
                                    
                                  
                                     
                                            <?= $form->field($model, 'seller_id')->widget(Select2::classname(), [
                                                    'data' => GiftCardSeller::giftCardSellerDropDownList(),
                                                    'language' => 'en-GB',
                                                    'options' => ['options' => GiftCardSeller::giftCardSellerOptionsDropDownList(), 'id' => 'dep_drop_seller', 'placeholder' => 'Select ' . $model->getAttributeLabel('seller_id')],
                                                    'pluginOptions' => [
                                                        'allowClear' => true
                                                    ],
                                                ]);
                                                ?>
                                      
                                        <?php
                                            if (empty($model->seller_account_id)) {
                                                echo $form->field($model, 'seller_account_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                                                    'options' => ['id' => 'seller_account_id'],
                                                    'pluginOptions' => [
                                                        'depends' => ['dep_drop_seller'],
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
                                      
                                           <?= $form->field($model,  'price')->textInput() ?>
                                     
                                            <?= $form->field($model,  'value')->textInput() ?>
                                       
                                            <?= $form->field($model,  'discount_rate')->textInput() ?>
                                       
                                            <?= $form->field($model, 'cash_back_website_id')->dropDownList(CashBackWebsite::cashBackWebsiteDropDownList(),['prompt'=>'Select '.$model->getAttributeLabel('cash_back_website_id')])->label("Website Cashback") ?>
                                       
                                            <?= $form->field($model,  'cash_back_website_rate')->label('Website Cashback %') ?>
                                       
                                        <?= $form->field($model,  'note')->label('Note') ?>
                                        

                                </div>

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
                                                        <tr>
                                                            <td>
                                                                <?= $form->field($paymentItem, "[$paymentIndex]id", ['template' => '{input}', 'options' => ['tag' => null]])->hiddenInput(['class' => 'field-id']); ?>
                                                                <?= $form->field($paymentItem, "[$paymentIndex]purchasing_account_type", ['template' => '{input}'])->dropDownList(CommonHelper::getAccountTypeDropDownList(), ['prompt' => 'Select Account Type', 'class' => 'form-control payment-method-account-type']) ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                $purchasingAccount = PurchasingAccount::purchasingAccountDropDownList($paymentItem->purchasing_account_type, true);
                                                                $purchasingAccount['items'] = !empty($purchasingAccount['items']) ? $purchasingAccount['items'] : [];
                                                                $purchasingAccount['options'] = !empty($purchasingAccount['options']) ? $purchasingAccount['options'] : [];
                                                                echo $form->field($paymentItem, "[$paymentIndex]purchasing_account_id", ['template' => '{input}'])->dropDownList($purchasingAccount['items'], ['prompt' => 'Select Card Number','options' => $purchasingAccount['options'], 'class' => 'form-control payment-method-account'])
                                                                ?>
                                                            </td>
                                                            <td>
                                                                Amount
                                                            </td>
                                                            <td>
                                                                <?= $form->field($paymentItem, "[$paymentIndex]spent_amount", ['template' => '{input}'])->textInput(['class' => 'form-control field-debit-amount']); ?>
                                                            </td>
                                                            <td class="payment-method-cash-back-label hide">
                                                                Cashback
                                                            </td>
                                                            <td class="payment-method-cash-back-value hide">
                                                                <?= $form->field($paymentItem, "[$paymentIndex]cash_back_rate", ['template' => '{input}<span class="input-box-percent-sign">%</span>'])->textInput(['class' => 'form-control field-cash-back-rate']); ?>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0)" class="remove-payment-method-row"><svg xmlns="http://www.w3.org/2000/svg" width="16.123" height="16.123" viewBox="0 0 16.123 16.123"><g transform="translate(-825.439 -620.439)"><circle cx="8.061" cy="8.061" r="8.061" transform="translate(825.439 620.439)" fill="#b2b2b2"/><g transform="translate(829.681 624.681)"><line x2="7.565" y2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/><line y1="7.565" x2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/></g></g></svg></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
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
                        <div class="col-sm-6 col-sm-offset-4">
                            <p id="form-submit-error-message" style="color:red;"></p>
                        </div>

                        <!-- <div class="col-sm-6 col-sm-offset-6">
                            <input id="delete-order-payment-method-ids" name="delete-order-payment-method-ids" type="hidden">
                            <?= FormButtons::widget(['item' => ['save', 'delete'], 'docId' => $model->doc_id]) ?>
                        </div> -->
                        
                        <div class=" float-right">
                        <?= FormButtons::widget(['item' => ['delete'], 'docId' => $model->doc_id]) ?>
                        <?= FormButtons::widget(['item' => ['save'], 'saveClassName' => 'saveGiftCardForm','docId' => $model->doc_id]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


<div class="hidden-field-for-javascript" style="display:none">
    <?= Html::dropDownList('purchasing_account_type_hidden_field', null, CommonHelper::getAccountTypeDropDownList(), ['prompt' => 'Select Account Type', 'id' => 'hidden-drop-down-field-payment-method-account-type']) ?>
</div>


<script type="text/javascript">
    $(document).ready(function () {


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

       
        $(document).on("change", ".payment-method-account", function () {
            var purchasingAccount = $(this).val();
            var paymentMethodCardType = $('#giftcardreload-purchasing_account_type').val();
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

        $(document).on("change", ".payment-method-gift-card-number", function () {
            var purchasingAccount = $(this).val();

            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/gift-card-balance' ?>',
                type: 'POST',
                data: {
                    accountNumber: purchasingAccount,
                    _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
                },
                success: function (data) {
                    $("#gift-card-balance").text('$' + data);
                }
            });
        });

    //Form Validation
    $('#gift-card-reload').on('beforeValidate', function (event) {
        var balance = $("#balance").html();
        var balanceAmount = balance.substring(1);
        var debitAmount   = parseFloat($("#giftcardreload-received_amount").val()) || 0;
        if(debitAmount > balanceAmount){
            $('#form-submit-error-message').html('Balance is insufficient');
            return false;
        }
    });


    })
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
                    $('#delete-order-payment-method-ids').val(deleteOrderPaymentMethodIds);
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

    });

    function preparePaymentMethodRow(indexPayment) {
        var html = '';

        html += '<tr>';

        // Account Type
        var paymentMethodTypeList = $('#hidden-drop-down-field-payment-method-account-type').html();


        html += '<td>' +
            '<div class="form-group field-transaction-' + indexPayment + '-purchasing_account_type">' +
            '<select id="transaction-' + indexPayment + '-purchasing_account_type" class="form-control payment-method-account-type" name="Transaction[' + indexPayment + '][purchasing_account_type]">'+paymentMethodTypeList+'</select>' +
            '</div>' +
            '</td>';

        // Card Number
        html += '<td>' +
            '<div class="form-group field-transaction-' + indexPayment + '-purchasing_account_id has-success">' +
            '<select id="transaction-' + indexPayment + '-purchasing_account_id" class="form-control payment-method-account" name="Transaction[' + indexPayment + '][purchasing_account_id]" aria-invalid="false" disabled="disabled">' +
            '<option value="">Select Card Number</option>' +
            '</select>' +
            '</div>' +
            '</td>';

        // Amount Text
        html += '<td>' +
            'Amount' +
            '</td>';

        // Amount Text Box
        html += '<td>' +
            '<div class="form-group field-transaction-' + indexPayment + '-spent_amount">' +
            '<input type="text" id="transaction-' + indexPayment + '-spent_amount" class="form-control field-debit-amount" name="Transaction[' + indexPayment + '][spent_amount]" aria-invalid="true">' +
            '</div>' +
            '</td>';

        // Cashback Field
        html += '<td class="payment-method-cash-back-label hide">' +
            'Cashback' +
            '</td>';

        // Cashback Amount
        html += '<td class="payment-method-cash-back-value hide">' +
            '<div class="form-group field-transaction-' + indexPayment + '-cash_back_rate">' +
            '<input type="text" id="transaction-' + indexPayment + '-cash_back_rate" class="form-control dynamic-field-validation field-cash-back-rate" name="Transaction[' + indexPayment + '][cash_back_rate]"  aria-invalid="false">' +
            '<span class="input-box-percent-sign">%</span>' +
            '</div>' +
            '</td>';

        // Delete Button
        html += '<td>' +
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
            if (cardType == 'credit-card' || cardType == 'pay-pal') {
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
            $("#sum-payment-amount").text(paymentTotal).css('color','black');
            $(".payment-mismitch").css('color','black');
        }

        var giftCardPrice = parseFloat($('#giftcardreload-price').val()) || 0;

        if(paymentTotal > giftCardPrice){
            $('#sum-payment-amount').html(paymentTotal.toFixed(2) + ' Exceed Gift Card Price').css('color','red');
            $(".payment-mismitch").css('color','red');
        }
    }

    calculateGiftCardDiscount();

    $(document).on('blur', '#giftcardreload-price, #giftcardreload-value', function () {
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
</script>

<script>

$("#gift-card-reload").on("submit", function(e) {
    e.preventDefault();
});

$(".saveGiftCardForm").unbind().on("click", function(e) {

        var purchaseForm = $("#gift-card-reload");
        
        var formData = purchaseForm.serializeArray();

        e.preventDefault();

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
                    }else{
                        $.each( obj, function( key, value ) {
                            $("#form-submit-error-message").html(value);
                            $("#form-submit-error-message").attr("style", "color:red");
                        });
                    }
                    setTimeout(function(){
                        $("#form-submit-error-message").attr("style", "display:none");
                    }, 5000);
                //
                    $('button.btn.btn-primary').html(" Save ");

                },
                error: function (data) {
                     $("#form-submit-error-message").html(data.responseText);
                     $("#form-submit-error-message").attr("style", "color:red");
                     setTimeout(function(){
                        $("#form-submit-error-message").attr("style", "display:none");
                    }, 5000);  
                },
                });   

});


</script>



