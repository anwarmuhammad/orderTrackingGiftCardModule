<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\core\buttons\FormButtons;
use unlock\modules\purchasingaccount\models\PurchasingAccount;
use unlock\modules\cashback\models\CashBackWebsite;
use unlock\modules\paymentmethod\models\TransactionPaymentMethod;
use unlock\modules\supplier\models\GiftCardSeller;
use unlock\modules\giftcard\models\GiftCard;
use yii\bootstrap\Modal;

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
        padding-left: 20px;
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
    .help-block-error {  display: none !important;  }
    .select2-container--open input.select2-search__field {
        width: 100% !important;
    }
    #dep_drop_seller{
        display:block !important;
    }
    #giftCardTransactionModalContent #gift-card-payment #dep_drop_seller{
        width: 100%!important;
        height: 30px!important;
        visibility: visible!important;
    }
    input#giftcardpayment-price {
        padding-left: 20px;
    }
    .singleAmount-total .amount-icon {
        top: 5px;
    }
    .psub2-total .amount-icon {
        position: absolute;
        left: 6px;
        top: 3px;
        font-size: 1rem;
        color: #888;
    }
    input#purchasingaccount-account,input#purchasingaccount-initial_balance,input#purchasingaccount-initial_balance_date,input#purchasingaccount-credit_limit,input#purchasingaccount-account_name,input#purchasingaccount-cash_back_credit_card_rate,input#purchasingaccount-cash_back_initial_balance
    ,input#purchasingaccount-cash_back_initial_balance_date{
        height: 40px;
    }
    .table-no-bordered .remove-payment-method-row svg {
        position: relative;
        left: 5px;
        color: #fff!important;
        top: 27px;
    }
</style>
<div class="main-container main-container-form" role="main">

    <div class="admin-form-container">
        <div class="panel panel-default">

            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',  //  'default', 'horizontal' or 'inline'
                    'id' => 'gift-card-payment',
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
                        <?= $form->field($model, 'gift_card_pay_date', ['template' => Yii::$app->getModule('core')->datePickerTemplate()])->widget(\yii\jui\DatePicker::className(),
                            [
                                'options' => ['class' => 'form-control'],
                                'language' => 'en-GB',
                                'value' => $model->gift_card_pay_date,
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


                            <div class="form-group field-supplier-account" id="dynamic-multiple-account">
                    <label class="control-label" for="supplier-account">Gift Card(s)</label>
                    <div>
                        <table class="table table-no-bordered dynamic-multiple-account-table">
                        <?php foreach ($modelGiftCardPaymentItems as $index => $item) { ?>
                                <?php if ($hasGiftCardPaymentItem) { ?>
                                    <tr id="dynamic-multiple-account">
                                        <td style="margin: 0;padding: 0">
                                            <?= $form->field($item, "[$index]id", ['template' => '{input}', 'options' => ['tag' => null]])->hiddenInput(['class' => 'field-gift-card-payment-id']); ?>
                                            <?= $form->field($item, "[$index]gift_card_id", ['template' => '{input}'])->dropDownList(GiftCard::giftCardDropDownList2($item['gift_card_id']), ['prompt' => 'Select Card Number', 'options' => GiftCard::giftCardOptionsDropDownList(), 'class' => 'form-control gift-card-number'])->label('Gift Card(s)') ?>
                                        </td>
                                        <td style="vertical-align: bottom;">
                                             <?= $form->field($item, "[$index]amount", ['template' => '{input}'])->textInput(['class' => 'form-control field-amount']); ?>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" class="remove-account-row-2"><svg xmlns="http://www.w3.org/2000/svg" width="16.123" height="16.123" viewBox="0 0 16.123 16.123"><g transform="translate(-825.439 -620.439)"><circle cx="8.061" cy="8.061" r="8.061" transform="translate(825.439 620.439)" fill="#b2b2b2"/><g transform="translate(829.681 624.681)"><line x2="7.565" y2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/><line y1="7.565" x2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/></g></g></svg></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>

                        </table>

                        <div class="add-another-btn " style="padding-bottom: 10px;">
                            <a href="javascript:void(0)" class="btn btn-add-more btn-add-new-matching-rule-row btn-add-new-account-row" id="add-new-account-row">
                                <i class="fa fa-plus-circle"></i> &nbsp;Add another
                            </a>
                        </div>
                    </div>
                          </div>
                                <table class="table table-no-bordered dynamic-content-table">
                                    <tr>
                                        <td>
                                        <?= $form->field($model, 'seller_id')->widget(Select2::classname(), [
                                                'data' => GiftCardSeller::giftCardSellerDropDownList2($model['seller_id']),
                                                'language' => 'en-GB',
                                                'options' => ['options' => GiftCardSeller::giftCardSellerOptionsDropDownList(), 'id' => 'dep_drop_seller', 'placeholder' => 'Select ' . $model->getAttributeLabel('seller_id')],
                                                'pluginOptions' => [
                                                    'allowClear' => true
                                                ],
                                            ])->label('Seller<span class="text-danger">*</span>');
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        <?php
                                            /*if (empty($model->seller_account_id)) {
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
                                            }*/
                                            if (empty($model->seller_account_id)) {
                                                echo $form->field($model, 'seller_account_id')->dropDownList(['prompt' => 'Select'])->label('Seller Account<span class="text-danger">*</span>');
                                            }else{
                                                echo $form->field($model, 'seller_account_id')->dropDownList(['prompt' => 'Select'])->label('Seller Account<span class="text-danger">*</span>');
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?= $form->field($model, 'price', ['template' => '{label}<div class="nopadding"><div class="psub2-total"><span class="amount-icon">$</span>{input}{error}</div></div>'])->textInput(['maxlength' => true])->label('Price<span class="text-danger">*</span>') ?>
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
                                                <h3>Payment Methods<span class="text-danger">*</span></h3>
                                            </div>
                                            <div class="box-content">
                                                <table class="table table-no-bordered">

                                                    <tbody>
                                                    <?php foreach ($modelPaymentMethodItems as $paymentIndex => $paymentItem){ ?>
                                                        <?php if($hasPaymentMethodItem) { ?>
                                                            <tr>
                                                                <td style="vertical-align: top;">
                                                                    <div class="paymentDoc-label">Type<span class="text-danger">*</span></div>

                                                                    <?= $form->field($paymentItem, "[$paymentIndex]id", ['template' => '{input}', 'options' => ['tag' => null]])->hiddenInput(['class' => 'field-id']); ?>
                                                                    <?= $form->field($paymentItem, "[$paymentIndex]purchasing_account_type", ['template' => '{input}'])->dropDownList(CommonHelper::getAccountTypePaymentDropDownList(), ['prompt' => 'Select Account Type', 'class' => 'form-control payment-method-account-type']) ?>
                                                                </td>
                                                                <td style="vertical-align: top;">
                                                                    <div class="paymentDoc-label">Account<span class="text-danger">*</span></div>

                                                                    <?php
                                                                    $purchasingAccount = PurchasingAccount::purchasingAccountDropDownList2($paymentItem['purchasing_account_type'], $paymentItem['purchasing_account_id'], true);
                                                                    $purchasingAccount['items'] = !empty($purchasingAccount['items']) ? $purchasingAccount['items'] : [];
                                                                    $purchasingAccount['options'] = !empty($purchasingAccount['options']) ? $purchasingAccount['options'] : [];
                                                                    echo $form->field($paymentItem, "[$paymentIndex]purchasing_account_id", ['template' => '{input}'])->dropDownList($purchasingAccount['items'], ['prompt' => 'Select Card Number','options' => $purchasingAccount['options'], 'class' => 'form-control payment-method-account','disabled'=>true])
                                                                    ?>
                                                                </td>
                                                                <!-- <td style="vertical-align: middle;">
                                                                    Amount
                                                                </td> -->
                                                                <td style="vertical-align: top;max-width: 120px;">
                                                                    <div class="paymentDoc-label">Amount<span class="text-danger">*</span></div>

                                                                    <?= $form->field($paymentItem, "[$paymentIndex]spent_amount", ['template' => '<div class="nopadding"><div class="singleAmount-total"><span class="amount-icon">$</span>{input}</div></div>{error}'])->textInput(['class' => 'form-control field-debit-amount purchase-order-payment-auto-save']); ?>
                                                                </td>
                                                                <!-- <td style="vertical-align: middle;" class="payment-method-cash-back-label hide">
                                                                    Cashback
                                                                </td> -->
                                                                <td class="payment-method-cash-back-value hide" style="max-width: 120px;">
                                                                    <div class="paymentDoc-label">Cashback</div>

                                                                    <?= $form->field($paymentItem, "[$paymentIndex]cash_back_rate", ['template' => '{input}<span class="input-box-percent-sign">%</span>'])->textInput(['class' => 'form-control field-cash-back-rate']); ?>
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:void(0)" class="remove-payment-method-row"><svg xmlns="http://www.w3.org/2000/svg" width="16.123" height="16.123" viewBox="0 0 16.123 16.123"><g transform="translate(-825.439 -620.439)"><circle cx="8.061" cy="8.061" r="8.061" transform="translate(825.439 620.439)" fill="#b2b2b2"/><g transform="translate(829.681 624.681)"><line x2="7.565" y2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/><line y1="7.565" x2="7.565" transform="translate(0 0)" fill="none" stroke="#fff" stroke-width="2"/></g></g></svg></a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    </tbody>

                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="5" height="10"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3">

                                                                <div class="add-another-btn" style="">
                                                                    <a href="javascript:void(0)" class="btn btn-add-more add-new-payment-row" id="add-new-payment-row" style="top: 0px; left: 0px;">
                                                                        <i class="fa fa-plus-circle"></i> &nbsp;Add Payment Method
                                                                    </a>
                                                                </div>
                                                            </td>
                                                            <td colspan="2">
                                                                <div>
                                                                    <label>Total: $<span id="sum-payment-amount" data-color="">0</span></label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tfoot>

                                                </table>

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
                            <?= FormButtons::widget(['item' => ['save', 'delete'], 'docId' => $model->doc_id]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>


<div class="hidden-field-for-javascript" style="display:none">
    <?= $form->field($item, "[$index]gift_card_id", ['template' => '{input}'])->dropDownList(GiftCard::giftCardDropDownList(), ['prompt' => 'Select Card Number', 'options' => GiftCard::giftCardOptionsDropDownList(), 'class' => 'form-control gift-card-number','id' => 'hidden-drop-down-field-gift-card-number']) ?>
    <?= Html::dropDownList('purchasing_account_type_hidden_field', null, CommonHelper::getAccountTypePaymentDropDownList(), ['prompt' => 'Select Account Type', 'id' => 'hidden-drop-down-field-payment-method-account-type']) ?>
</div>

<?php

// Gift Card Modal Popup

\yii\bootstrap\Modal::begin([
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

\yii\bootstrap\Modal::end();

?>
<?php
//Add New Purchasing Account
Modal::begin([
    'header' => '<h4 class="modal-title" id="purchasingAccount-header-content"></h4>',
    'id'     => 'newPurchasingAccountModalContent',
    'size'   => 'modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered',
    'closeButton' => [
        'id'=>'close-button-purchasingAccount-create',
        'class'=>'modal-in-close-btn',
        'data-dismiss' =>'modal5',
        //            'label' =>'A',

    ],
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
]);
echo "<div id='add-newPurchasingAccountModalContent'></div>";
Modal::end();
?>

<script type="text/javascript">
    $(document).ready(function () {

        $('.saveAjax').off().on('click', function () {
            $('.help-block-error').attr('style', 'display:block !important;');
        });

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

        //Default CashBack Website and % w.r.t Seller Url::to(['/ajax/ajax/seller-account-dep-drop-down-list']

        $(document).on("change", "#dep_drop_seller", function () {

            var sellerId = $(this).val();

            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/seller-account-dep-drop-down-list' ?>',
                type: 'POST',
                data: {
                    sellerId: sellerId,
                    select2: 0,
                    _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
                },
                success: function (data) {
                    $("#giftcardpayment-seller_account_id").html(data);
                    $("#giftcardpayment-seller_account_id").removeAttr('disabled');
                }
            });

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

        
    function reloadGiftCard(){

        var refDocId = '<?php echo $refDocId ?>';


        $("#inbox-content-view").html('');
        $("#inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");


        var updateButtonUrl = '<?= Url::toRoute(['/gift-card/gift-card/update'])?>?docId=' + refDocId;
        $('#inboxModal').modal('show')
            .find('#inbox-content-view')
            .load(updateButtonUrl);
    }

    //Form Validation
    $('#gift-card-payment').on('beforeValidate', function (event) {

        var paymentTotal = 0;

        $('#box-payment-method .box-content tbody tr').each(function () {
            paymentTotal += parseFloat($(this).find('.field-debit-amount').val()) || 0;
        });

        if (!isNaN(paymentTotal)) {
            $("#sum-payment-amount").text(paymentTotal.toFixed(2)).css('color','black');
            $(".payment-mismitch").css('color','black');
        }

        var giftCardTotalPrice = parseFloat($('#giftcardpayment-price').val()) || 0;

        if(paymentTotal != giftCardTotalPrice){
            $('#form-submit-error-message').html('Total Pay Amount must be equal to Gift Card Price ');
            return false;
        }

    });

        $('#gift-card-payment').on('beforeSubmit', function(event) {

            event.stopImmediatePropagation();
            var purchaseForm = $("#gift-card-payment");
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
                        setTimeout(function(){

                            reloadGiftCard();
                            // reloadMainPopupAreaFunc();
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

        $(document).off('click', '.remove-account-row').on('click', '.remove-account-row', function () {
            var _this = $(this);
            iziToast.show({
                buttons: [
                    ['<button class="mt-3 float-left btn"><b>YES</b></button>', function (instance, toast) {

                        instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');

                        var accountId = _this.closest("tr").find('.field-gift-card-payment-id').val();
                        if (typeof accountId != "undefined" && accountId != "" && accountId != null) {
                            deleteGiftCardPaymentItemIds.push(accountId);
                            $('#delete-gift-card-payment-item-ids').val(deleteGiftCardPaymentItemIds);
                        }

                        _this.closest("tr").remove();

                    }, true],
                    ['<button class="mt-3 float-right btn">NO</button>', function (instance, toast) {

                        instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');

                    }],
                ]
            });
        });

        $(document).off('click', '.remove-account-row-2').on('click', '.remove-account-row-2', function () {
            var _this = $(this);
            iziToast.show({
                buttons: [
                    ['<button class="mt-3 float-left btn"><b>YES</b></button>', function (instance, toast) {

                        instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');

                        var accountId = _this.closest("tr").find('.field-gift-card-payment-id').val();
                        if (typeof accountId != "undefined" && accountId != "" && accountId != null) {
                            deleteGiftCardPaymentItemIds.push(accountId);
                            $('#delete-gift-card-payment-item-ids').val(deleteGiftCardPaymentItemIds);
                        }

                        _this.closest("tr").remove();

                    }, true],
                    ['<button class="mt-3 float-right btn">NO</button>', function (instance, toast) {

                        instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');

                    }],
                ]
            });
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
        html += '<td style="vertical-align: bottom;">' +
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
        $(document).off('click', '.remove-payment-method-row').on('click', '.remove-payment-method-row', function () {
            var _this = $(this);
            iziToast.show({
                buttons: [
                    ['<button class="mt-3 float-left btn"><b>YES</b></button>', function (instance, toast) {

                        instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');

                        var paymentMethodId = _this.closest("tr").find('.field-id').val();
                        if (typeof paymentMethodId != "undefined" && paymentMethodId != "" && paymentMethodId != null) {
                            deleteOrderPaymentMethodIds.push(paymentMethodId);
                            $('#delete-payment-method-ids').val(deleteOrderPaymentMethodIds);
                        }

                        _this.closest("tr").remove();

                    }, true],
                    ['<button class="mt-3 float-right btn">NO</button>', function (instance, toast) {

                        instance.hide({ transitionOut: 'fadeOutUp' }, toast, 'button');

                    }],
                ]
            });
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

            //--------------------------Add new payment account-----------------------------
            var paymentAccountId = $(this).val();
            var cardType = $(this).closest('tr').find('.payment-method-account-type').val();
            var dropDownId = $(this).attr("id");
            var row_id = $(this).attr('id').split('-');
            var previousAccount = '******'+$('#seleted_account_data_'+row_id[1]).val();
            var previousAccountId = $('#seleted_account_id_data_'+row_id[1]).val();

            if(paymentAccountId == 0){
                var createFrom = 'purchaseOrderPayment';

                var loadPurchasingAccountUrl = '<?= Url::toRoute(['/purchasing-account/purchasing-account/create'])?>?createFrom='+ createFrom  + '&dropDownId=' + dropDownId+'&cardType='+cardType;

                $("#add-newPurchasingAccountModalContent").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");

                $('#purchasingAccount-header-content').html('Add Payment Account');
                $('#newPurchasingAccountModalContent').modal('show')
                    .find('#add-newPurchasingAccountModalContent')
                    .load(loadPurchasingAccountUrl);

                $('#newPurchasingAccountModalContent').find('.modal-header').css({'padding' : '1rem 1rem 0 0'});
                $('#newPurchasingAccountModalContent').find('.modal-body').css({'padding' : '1rem'});
                $('#newPurchasingAccountModalContent').find('.modal-title').css({'display' : 'block'});


                $('#purchasingaccount-account').val(cardType).trigger('change');

                //Close
                $('#close-button-purchasingAccount-create').click(function(){

                    $('#newPurchasingAccountModalContent').modal('hide');

                    //alert('previousAccountId: '.previousAccountId);

                    if(previousAccountId){

                        $('#'+dropDownId+' option[value ="'+previousAccountId+'"]').remove();
                        $("#"+dropDownId+"").append('<option value="'+previousAccountId+'"  selected>'+ previousAccount +'</option>');
                    }
                    else{
                        $("#"+dropDownId+" option").eq(0).remove();
                        $("#"+dropDownId+"").prepend('<option value=""  selected></option>');
                    }
                });
            }
            //--------------------------End Add new payment account----------------------

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

        html += '<tr>';

        // Account Type
        var paymentMethodTypeList = $('#hidden-drop-down-field-payment-method-account-type').html();


        html += '<td style="vertical-align: top;">' +
            '<div class="paymentDoc-label">Type<span class="text-danger">*</span></div>'+
            '<div class="form-group field-transaction-' + indexPayment + '-purchasing_account_type">' +
            '<select id="transaction-' + indexPayment + '-purchasing_account_type" class="form-control payment-method-account-type" name="Transaction[' + indexPayment + '][purchasing_account_type]">'+paymentMethodTypeList+'</select>' +
            '</div>' +
            '</td>';

        // Card Number
        html += '<td style="vertical-align: top;">' +
            '<div class="paymentDoc-label">Account<span class="text-danger">*</span></div>'+
            '<div class="form-group field-transaction-' + indexPayment + '-purchasing_account_id has-success">' +
            '<select id="transaction-' + indexPayment + '-purchasing_account_id" class="form-control payment-method-account" name="Transaction[' + indexPayment + '][purchasing_account_id]" aria-invalid="false" disabled="disabled">' +
            '<option value="">Select Card Number</option>' +
            '</select>' +
            '</div>' +
            '</td>';

        // Amount Text
        // html += '<td style="vertical-align: middle;">' +
        //     'Amount' +
        //     '</td>';

        // Amount Text Box
        // html += '<td style="vertical-align: top;max-width: 120px;">' +
        //     '<div class="form-group field-transaction-' + indexPayment + '-spent_amount">' +
        //     '<input type="text" id="transaction-' + indexPayment + '-spent_amount" class="form-control field-debit-amount" name="Transaction[' + indexPayment + '][spent_amount]" aria-invalid="true">' +
        //     '</div>' +
        //     '</td>';

        html += '<td style="vertical-align: top;max-width: 120px;">' +
            '<div class="paymentDoc-label">Amount<span class="text-danger">*</span></div>'+
            '<div class="form-group field-transaction-'+indexPayment+'-spent_amount has-success">\n' +
            '<div class="nopadding"><div class="singleAmount-total"><span class="amount-icon">$</span><input type="text" id="transaction-'+indexPayment+'-spent_amount" class="form-control field-debit-amount purchase-order-payment-auto-save" name="Transaction['+indexPayment+'][spent_amount]" aria-invalid="false"></div></div><p class="help-block help-block-error "></p>\n' +
            '</div>' +
            '</td>';

        // Cashback Field
        // html += '<td  style="vertical-align: middle;" class="payment-method-cash-back-label hide">' +
        //     'Cashback' +
        //     '</td>';

        // Cashback Amount
        html += '<td class="payment-method-cash-back-value hide" style="max-width: 120px;">' +
            '<div class="paymentDoc-label">Cashback</div>'+
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
            $("#sum-payment-amount").text(paymentTotal).css('color','black');
            $(".payment-mismitch").css('color','black');
        }

        var giftCardPrice = parseFloat($('#giftcardpayment-price').val()) || 0;

        if(paymentTotal > giftCardPrice){
            $('#sum-payment-amount').html(paymentTotal.toFixed(2) + ' Exceed Gift Card Price').css('color','red');
            $(".payment-mismitch").css('color','red');
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
</script>
