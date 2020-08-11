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

/* @var $this yii\web\View */
/* @var $model unlock\modules\purchaseorder\models\PurchaseOrder */
/* @var $form yii\bootstrap\ActiveForm */



$isNewRecord = $model->isNewRecord;

$this->title = ($model->isNewRecord) ? Yii::t('app', 'Create Gift Card Charge') : Yii::t('app', 'Update Gift Card Charge');
?>

<style>
    .field-purchaseorderpayment-payment_date .help-block {
        display: none;
    }
</style>
<div class="main-container main-container-form" role="main">
    <div class="page-header">
        <div class="page-header-title">
            <h1 class="page-title"><?= Yii::t('app', 'Gift Card Charge') ?></h1>
        </div>

    </div>

    <div class="admin-form-container">
        <div class="panel panel-default">

            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',  //  'default', 'horizontal' or 'inline'
                    'id' => 'charge-account',
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

                <div class="panel-box panel-box-bg" id="order-form-header"  style="padding: 3px 0px 0px 0px">
                    <div class="box-content">
                        <div class="row">
                            <div class="col-sm-3">
                                <?php if(!$isNewRecord) { ?>
                                    <p style="padding: 0px 0px 0px 24px"><?= $form->field($model, 'doc_id', ['template' => $model->doc_id, 'options' => ['tag' => false] ])->hiddenInput() ?> </p>
                                <?php } ?>
                            </div>
                            <div class="col-sm-6">&nbsp;</div>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'purchasing_account_charge_date', ['template' => Yii::$app->getModule('core')->datePickerTemplate()])->widget(\yii\jui\DatePicker::className(),
                                    [
                                        'options' => ['class' => 'form-control'],
                                        'language' => 'en-GB',
                                        'value' => $model->purchasing_account_charge_date,
                                        'dateFormat' => 'MM/dd/yyyy',
                                        'clientOptions' => [
                                            'changeYear' => true,
                                            'changeMonth' => true,
                                            'todayHighlight' => true,
                                        ],
                                    ]) ?>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="row" style="padding-top: 15px; padding-bottom: 15px">
                    <div class="col-sm-10 col-sm-offset-1">
                        <div class="panel-box panel-box-border" id="box-payment-method">
                            <div class="box-content">
                                <table class="table table-no-bordered">

                                    <tr>
                                        <td>
                                            <?= $form->field($model, "purchasing_account_type")->dropDownList(CommonHelper::getAccountTypeDropDownList(), ['prompt' => 'Select Account Type', 'class' => 'form-control payment-method-account-type'])->label('Account')?>
                                        </td>
                                        <td>
                                            <?php
                                            $purchasingAccount = PurchasingAccount::purchasingAccountDropDownList($model->purchasing_account_type, true);
                                            $purchasingAccount['items'] = !empty($purchasingAccount['items']) ? $purchasingAccount['items'] : [];
                                            $purchasingAccount['options'] = !empty($purchasingAccount['options']) ? $purchasingAccount['options'] : [];
                                            echo $form->field($model, "purchasing_account_id", ['template' => '{input}'])->dropDownList($purchasingAccount['items'], ['prompt' => 'Select Card Number', 'options' => $purchasingAccount['options'], 'class' => 'form-control payment-method-account'])
                                            ?>
                                        </td>
                                        <td>
                                            Balance &ensp;<span id='balance'>$0</span>
                                        </td>
                                        <td>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <?= $form->field($model, 'cash_back_credit_card_rate')->textInput() ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?= $form->field($model, 'supplier_id')->dropDownList(['0' => '-- Add New --']+Supplier::supplierDropDownList(),['prompt'=>'Select '.$model->getAttributeLabel('supplier_id')]) ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" height="10"></td>
                                    <tr>
                                        <td>
                                            <?= $form->field($model, 'category_id')->dropDownList(Category::categoryDropDownList(), ['prompt' => 'Select ' . $model->getAttributeLabel('category_id')])->label('Category')  ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?= $form->field($model,  'amount')->label('Amount') ?>
                                            <div class="col-sm-6 col-sm-offset-4">
                                                <p id="form-submit-error-message" style="color:red;"></p>
                                            </div>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>
                                            <?= $form->field($model,  'order_number')->label('Order Id') ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?= $form->field($model, 'cash_back_website_id')->dropDownList(CashBackWebsite::cashBackWebsiteDropDownList(),['prompt'=>'Select '.$model->getAttributeLabel('cash_back_website_id')])->label("Website Cashback") ?>
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

                            </div>
                        </div>
                    </div>
                </div>

                <div class='row'>&nbsp;</div>

                <div class="admin-form-button">
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-4">
                            <p id="form-submit-error-message" style="color:red;"></p>
                        </div>

                        <div class="col-sm-6 col-sm-offset-6">
                            <?= FormButtons::widget(['item' => ['save', 'delete'], 'docId' => $model->doc_id]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        // Account Balance
        var paymentAccountType  = $("#purchasingaccountcharge-purchasing_account_type").val();
        var paymentAccountId    = $("#purchasingaccountcharge-purchasing_account_id").val();

        loadAccountBalance(paymentAccountType, paymentAccountId);

        $("#purchasingaccountcharge-purchasing_account_id").change(function () {
            var paymentAccountType  = $("#purchasingaccountcharge-purchasing_account_type").val();
            var paymentAccountId    = $(this).val();
            loadAccountBalance(paymentAccountType, paymentAccountId);
        });

        // From Account
        $("#purchasingaccountcharge-purchasing_account_type").change(function () {
            var paymentAccountType = $(this).val();
            loadPaymentAccountDropDownList("#purchasingaccountcharge-purchasing_account_id", paymentAccountType);
        });

        //Form Validation
        $('#charge-account').on('beforeValidate', function (event) {
            var balance = $("#balance").html();
            var balanceAmount = balance.substring(1);
            var debitAmount   = parseFloat($("#purchasingaccountcharge-spent_amount").val()) || 0;
            if(debitAmount > balanceAmount){
                $('#form-submit-error-message').html('Balance is insufficient');
                return false;
            }
        });

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
                $("#balance").html('$' + data);
                amountFieldEnable(data);
            }
        });
    }

    function loadPaymentAccountDropDownList(fieldId, paymentAccountType)
    {
        $.ajax({
            url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/load-payment-account-list-by-account-type' ?>',
            type: 'POST',
            data: {
                paymentAccountType: paymentAccountType,
                _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
            },
            success: function (data) {
                $(fieldId).html(data);
            }
        });
    }

    function amountFieldEnable(balance) {
        if (balance > 0)
        {
            $("#purchasingaccountcharge-amount").prop('disabled', false);
        }else {
            $("#purchasingaccountcharge-amount").prop('disabled', true);
        }
    }
</script>
