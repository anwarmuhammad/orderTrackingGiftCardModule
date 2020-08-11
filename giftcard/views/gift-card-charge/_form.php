<?php
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;
use unlock\modules\category\models\Category;
use unlock\modules\core\buttons\FormButtons;
use unlock\modules\supplier\models\Supplier;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\cashback\models\CashBackWebsite;
use unlock\modules\purchasingaccount\models\PurchasingAccount;
use unlock\modules\paymentmethod\models\TransactionPaymentMethod;

/* @var $this yii\web\View */
/* @var $model unlock\modules\purchaseorder\models\PurchaseOrder */
/* @var $form yii\bootstrap\ActiveForm */



$isNewRecord = $model->isNewRecord;

$this->title = ($model->isNewRecord) ? Yii::t('app', 'Create Payment Account Charge') : Yii::t('app', 'Update Payment Account Charge');
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
    .show-pass-gift a{
        position: relative;
        top: 8px;
    }
    .btn-add-more{
        position: relative;
        top: -15px;
        left: -12px;
    }
    #show_hide_password_payment_account input{
        margin-right: 9px;
    }
    .box-content table td{
        padding: 0 .75rem 10px .75rem;
    }

    .control-label {
        margin-bottom: 0;
        color: #888888;
        font-family: 'Kanit-Light';
        font-size: 14px!important;
    }
    #purchasingaccount-account_type
    {
        padding: .375rem .4rem!important;
    }
    .gift-card-content{
        padding-bottom: 15px;
    }
    .form-control{
        color: #888888;
    }

    .payment-account-modal-title{
        color: #888888!important;
        font-family: 'Kanit-Light'!important;
    }
    .modal-header{
        border: none!important;
        margin-left: 86px;
        padding-bottom: 0;
    }
    #inbox-content-view .modal-body{
        padding-top: 0;
    }
    .payment-transaction-date label{
        display: inline-block;
        float: left;
        margin-right: 10px;
        padding-top: 2px;
    }
    .payment-transaction-date .field-purchasingaccountcharge-purchasing_account_charge_date{
        position: relative;
        right: 12px;
        float: right;
    }

    #box-payment-method .box-content #purchasingaccountcharge-purchasing_account_charge_date{
        height: auto!important;
        color: #fff!important;
    }
    .payment-transaction-date .input-group-datepicker{
        width: 122px;
    }
    
    .modal2 {
        float: right;
        /* font-size: 1.5rem; */
        font-weight: 700;
        line-height: 1;
        color: #000;
        text-shadow: 0 1px 0 #fff;
        opacity: .5;
        background-color: #fff;
        align-self: flex-end;
        font-size: 2.5rem;
        padding: 0rem 1rem !important;
    }
    .modal-header {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: start;
        align-items: flex-start;
        -ms-flex-pack: justify;
        justify-content: space-between;
        padding: 1rem 1rem;
        border-bottom: 1px solid #8c368c!important;
        border-top-left-radius: 0.3rem;
        border-top-right-radius: 0.3rem;
        margin: 0 41px 0 36px;
        padding-left: 0!important;
    }
    .modal-title-category {
        position: relative;
        top: 7px;
    }
    .btn-color{
        background: #8C368C;
        border: #8C368C;
        border-radius: 62px;
        color: #fff;
        text-transform: uppercase;
        padding: 4px 35px;
        font-size: 14px;
    }
    .admin-form-button{
        background: white !important;
    }
    .psub2-total .amount-icon {
        left: 5px!important;
        top: 2px!important;
    }
    input#purchasingaccountcharge-amount {
        padding-left: 18px;
    }

    /* ------------------ BEGIN Category pop-up Standardization ----------------- */
    @media (min-width: 576px){
        #modal-category-management .modal-dialog 
        {
            max-width: 350px;
            margin: 1.75rem auto;
        }
    }
    #modal-category-management .modal-header 
    {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: start;
        align-items: flex-start;
        -ms-flex-pack: justify;
        justify-content: space-between;
        padding: 1rem 0 5px 1rem;
        border-bottom: 1px solid #8c368c!important;
        border-top-left-radius: 0.3rem;
        border-top-right-radius: 0.3rem;
        margin: 0 41px 0 36px;
        padding-left: 0!important;
    }

    #modal-category-management .modal2 
    {
        float: right;
        /* font-size: 1.5rem; */
        font-weight: 100;
        line-height: 0.6;
        color: #ddd;
        text-shadow: 0 1px 0 #fff;
        opacity: 1;
        background-color: #fff;
        align-self: flex-end;
        font-size: 3.5rem;
        padding: 0rem 0 0px 1rem !important;
    }
    /* ------------------- END Category pop-up Standardization ------------------ */

</style>
<?php
if (Yii::$app->request->isAjax) {
    Yii::$app->assetManager->bundles['yii'] = false;
}

?>
<div class="main-container main-container-form" role="main">


    <div class="admin-form-container">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',  //  'default', 'horizontal' or 'inline'
                    'id' => 'charge-account',
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
                            <div class="col-sm-3" style="display: none;">
                                <?php if(!$isNewRecord) { ?>
                                    <p style="padding: 0px 0px 0px 24px"><?= $form->field($model, 'doc_id', ['template' => $model->doc_id, 'options' => ['tag' => false] ])->hiddenInput() ?> </p>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="panel-box panel-box-border" id="box-payment-method" style="overflow: hidden; height: calc(100vh - 222px); border: 1px solid #8c368c; margin: 0 15px; padding: 12px 5px; border-radius: 3px; margin-bottom: 20px; margin: 0 auto; width: 93%;">
                    <div class="box-content" style="position:relative;overflow-y: auto; max-height: calc(100vh - 250px);">
                        <div class="payment-transaction-date">
                            <?= $form->field($model, 'purchasing_account_charge_date', ['template' => Yii::$app->getModule('core')->datePickerTemplate()])->widget(\yii\jui\DatePicker::className(),
                                [
                                    'options' => ['class' => 'form-control'],
                                    'language' => 'en-GB',
                                    'value' => $model->purchasing_account_charge_date,
                                    'dateFormat' => 'MM / dd / yyyy',
                                    'clientOptions' => [
                                        'changeYear' => true,
                                        'changeMonth' => true,
                                        'todayHighlight' => true,
                                    ],
                                ]) ?>
                        </div>
                        <table class="table table-no-bordered">

                            <tr>
                                <td>
                                    <?= $form->field($model, "purchasing_account_type")->dropDownList(CommonHelper::getAccountTypeDropDownList(), ['prompt' => 'Select Account Type', 'class' => 'form-control payment-method-account-type','onchange'=>"showHideFunction()",'disabled'=>'disabled'])->label('Account<span class="text-danger">*</span>')?>
                                    <?= $form->field($model, "purchasing_account_type")->dropDownList(CommonHelper::getAccountTypeDropDownList(), ['prompt' => 'Select Account Type', 'class' => 'form-control payment-method-account-type','onchange'=>"showHideFunction()"])->label(false)->hiddenInput()?>
                                </td>
                                <td>
                                    <label style="height: 10px;">&nbsp;</label>
                                    <?php
                                    $purchasingAccount = PurchasingAccount::purchasingAccountDropDownList2($model['purchasing_account_type'], $model['purchasing_account_id'],true, $model['supplier_id']);
                                    $purchasingAccount['items'] = !empty($purchasingAccount['items']) ? $purchasingAccount['items'] : [];
                                    $purchasingAccount['options'] = !empty($purchasingAccount['options']) ? $purchasingAccount['options'] : [];
                                    echo $form->field($model, "purchasing_account_id", ['template' => '{input}'])->dropDownList($purchasingAccount['items'], ['prompt' => 'Select Card Number', 'options' => $purchasingAccount['options'], 'class' => 'form-control payment-method-account'])
                                    ?>
                                </td>
                                <td style="padding-left: 5px;">
                                    <label style="height: 23px; display: block;">&nbsp;</label>
                                    Balance &ensp;<span id='balance'><?php echo $balance;?></span>
                                </td>
                                <td style="padding-left: 5px;display: none;">
                                    <label style="height: 23px; ">&nbsp;</label>
                                    CreditLimit &ensp;<span id='creditLimit'><?php echo $creditLimit;?></span>
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
                                    <?= $form->field($model, 'supplier_id')->dropDownList(['0' => '-- Add New --']+Supplier::supplierDropDownList2($model['supplier_id']),['prompt'=>'Select '.$model->getAttributeLabel('supplier_id'),'disabled'=>'disabled'])->label('Supplier<span class="text-danger">*</span>')?>
                                    <?= $form->field($model, 'supplier_id')->dropDownList(['0' => '-- Add New --']+Supplier::supplierDropDownList2($model['supplier_id']),['prompt'=>'Select '.$model->getAttributeLabel('supplier_id')])->hiddenInput()->label(false)?>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="5" height="10"></td>
                            <tr>
                                <td>
                                    <?= $form->field($model, 'category_id')->dropDownList(['0' => '+ Add New']+Category::categoryDropDownList2($model['category_id']), ['prompt' => 'Select ' . $model->getAttributeLabel('category_id')])->label('Category<span class="text-danger">*</span>')  ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $form->field($model, 'amount', ['template' => '{label}<div class="nopadding"><div class="psub2-total"><span class="amount-icon">$</span>{input}{error}</div></div>'])->textInput(['maxlength' => true])->label('Amount<span class="text-danger">*</span>') ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $form->field($model,  'order_number')->label('Order Number') ?>
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

                    </div>
                </div>

                <div class="admin-form-button">
                    <div class="form-group">
                        <div class="">
                            <p id="charge-form-submit-error-message" style="color:red;"></p>
                        </div>

                        <div class="">
                            <!-- <?= FormButtons::widget(['item' => ['delete'], 'docId' => $model->doc_id]) ?> -->
                            <?= Html::a('<i class=""></i> ' . Yii::t('app', 'Delete'),Url::toRoute(['delete', 'docId' => $model->doc_id]), ['class' => 'btn btn-warning return_margin _delete disable-item', 'data-method'=>'post', 'data-url' => Url::toRoute(['delete', 'docId' => $model->doc_id]), 'id' => 'kanban-button-delete']) ?>
                            <?= FormButtons::widget(['item' => ['save'], 'saveClassName' => 'saveChargeForm','docId' => $model->doc_id]) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <!-- Modal add category-->
    <div class="modal fade" id="modal-category-management" role="dialog" data-keybord="false" data-backdrop="static">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title modal-title-category" style="color: #8C368C !important; font-weight: bold;">Category</h4>
                    <button class="btn  modal2" data-dismiss-modal="modal2"> <span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',  //  'default', 'horizontal' or 'inline'
                    /*'options' => ['enctype' => 'multipart/form-data'],*/
                    'id' => 'category-add-form',
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
                    <h6 id="message"></h6>
                    <?= $form->field($category, 'org_id')->hiddenInput(['value'=> CommonHelper::getLoggedInUserOrgId()])->label(false) ?>
                    <div class="box-content" style="margin: 15px;">
                        <table width="300">
                            <tbody>
                                <tr><?= $form->field($category, 'name')->textInput(['maxlength' => true])->label('Category Name<span class="text-danger">*</span>') ?></tr>

                                <tr><?= $form->field($category, 'type')->textInput(['maxlength' => true])->label('Type<span class="text-danger">*</span>') ?></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-12 mb-4 text-right">
                        <div id="form-submit-error-message-category"></div>
                        <button type="submit" class="btn btn-primary saveAjax btn-color  mt-2" name="action" value="save">Save</button>
                    </div>
                <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
    <!--  end Modal add category-->
</div>

<?php

Modal::begin([
    'header' => '<h4 class="modal-title" id="supplier-header-content"></h4>',
    'id'     => 'newSupplierModalContent',
    'size'   => 'modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered supplier-uniqe-modal',
    'closeButton' => [
        'id'=>'close-button-supplier-create',
        'class'=>'modal-in-close-btn',
        'data-dismiss' =>'modal5',
    ],
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
]);
echo "<div id='add-newSupplierModalContent'></div>";
Modal::end();

?>
<script type="text/javascript">
    $(document).ready(function() {

        $('#purchasingaccountcharge-category_id').off('change', '#purchasingaccountcharge-category_id').change(function(){

            if( $('#purchasingaccountcharge-category_id').val() == '0')
            {
                $('#category-name').val('');
                $('#category-type').val('');
                $("#form-submit-error-message-category").html('');
                $('#modal-category-management').modal({show:true});
            }
        });

        $("button[data-dismiss-modal=modal2]").off("click", "button[data-dismiss-modal=modal2]").click(function(){
        var category_id = '<?= $model->category_id; ?>';
        if(category_id){
            $('#purchasingaccountcharge-category_id').val(category_id);
        }
        else{
            $('#purchasingaccountcharge-category_id').val('');
        }

        $('#modal-category-management').modal('hide');
        });

        $('#category-add-form').on('beforeSubmit', function(e) {
            alert("category");
            e.preventDefault();
            // alert('submit');
            var form = $("#category-add-form");

            //var formData = form.serialize();
            var formData = form.serializeArray();
            console.log(formData);
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/add-new-category' ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (data) {
                    console.log('DATA:'+data);
                    var obj = jQuery.parseJSON(JSON.stringify(data) );

                    if(data.created){
                        $("#form-submit-error-message-category").html("Successfully Saved Data.");
                        $("#form-submit-error-message-category").attr("style", "color:green");
                        $('#purchasingaccountcharge-category_id').append('<option value="'+data.id+'">'+data.name+'</option>');
                        $('#purchasingaccountcharge-category_id').val(data.id);
                        setTimeout(function(){
                            $('#modal-category-management').modal('hide');
                        }, 1000)
                    }else{
                        $.each( obj, function( key, value ) {
                            $("#form-submit-error-message-category").html(value);
                            $("#form-submit-error-message-category").attr("style", "color:red");
                        });
                    }
                },
                error: function () {
                    alert("Something went wrong")
                },
                beforeSend: function (xhr) {
                    $('#category-form-id button.btn.btn-primary').html("<span class=\"fa fa-spin fa-spinner\"></span> Processing...");
                },
            });
        }).on('submit', function(e){

            e.preventDefault();

        });

        $("#purchasingaccountcharge-purchasing_account_id").change(function () {
            var paymentAccountType  = $("#purchasingaccountcharge-purchasing_account_type").val();
            var paymentAccountId    = $(this).val();
            loadAccountBalance(paymentAccountType, paymentAccountId);
            getCreditLimit(paymentAccountType, paymentAccountId);
        });

        // From Account
        $("#purchasingaccountcharge-purchasing_account_type").change(function () {
            var paymentAccountType = $(this).val();
            loadPaymentAccountDropDownList("#purchasingaccountcharge-purchasing_account_id", paymentAccountType);
        });

        //Form Validation
        $('#charge-account').on('beforeValidate', function (event) {

            var paymentAccountType  = $("#purchasingaccountcharge-purchasing_account_type").val();
            var paymentAccountId    = $("#purchasingaccountcharge-purchasing_account_id").val();
            var creditLimit = $("#creditLimit").html();
            var creditLimitAmount = Number(creditLimit.replace(/[^0-9.-]+/g,""));
            var balance = $("#balance").html();
            var balanceAmount = Number(balance.replace(/[^0-9.-]+/g,""));
            var debit   = parseFloat($("#purchasingaccountcharge-amount").val()) || 0;

            var debitAmount = Number(debit);

                if(debitAmount > balanceAmount){
                    $('#charge-form-submit-error-message').html('Insufficient Balance');
                    return false;
                }
        });

    });

    //Get Credit Limit
    function getCreditLimit(paymentAccountType, paymentAccountId) {
        $.ajax({
            url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/load-payment-account-credit-limit' ?>',
            type: 'POST',
            data: {
                paymentAccountType: paymentAccountType,
                paymentAccountId: paymentAccountId,
                _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
            },
            success: function (data) {
                $("#creditLimit").html('$' + data);
            }
        });
    }

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
                // amountFieldEnable(data);
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
        if (parseFloat(balance).toFixed(2)> 0)
        {
            $("#purchasingaccountcharge-amount").prop('disabled', false);
        }else {
            $("#purchasingaccountcharge-amount").prop('disabled', true);
        }
    }

    $('.saveChargeForm').on('click', function () {
        $('.help-block-error').attr('style', 'display:block !important;');
    });

    $('#charge-account').on('beforeSubmit', function(event) {

        event.stopImmediatePropagation();
        var purchaseForm = $("#charge-account");
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
                    $("#charge-form-submit-error-message").html("Successfully Saved Data.");
                    $("#charge-form-submit-error-message").attr("style", "color:green");
                    setTimeout(function(){// wait for 5 secs(2)
                        //location.reload(); // then reload the page.(3)
                        reloadMainPopupAreaFunc();
                    }, 1000)
                }else if(data.doc == "giftCard"){

                    $("#charge-form-submit-error-message").html("Successfully Saved Data.");
                    $("#charge-form-submit-error-message").attr("style", "color:green");

                    setTimeout(function(){// wait for 5 secs(2)

                        $("#inbox-content-view").html('');
                        $("#inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");
                        var docId =data.doc_id;

                        var loaded_url = '<?= Url::toRoute(['/gift-card/gift-card/update?docId=']) ?>'+ docId;

                        $('#inboxModal').modal('show')
                            .find('#inbox-content-view')
                            .load(loaded_url);

                        //location.reload(); // then reload the page.(3)
                        // reloadMainPopupAreaFunc();
                    }, 1000)
                }else{
                    $.each( obj, function( key, value ) {
                        $("#charge-form-submit-error-message").html(value);
                        $("#charge-form-submit-error-message").attr("style", "color:red");
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

    function showHideFunction() {
        var accountType = document.getElementById("purchasingaccountcharge-purchasing_account_type").value;

        // alert(accountType);
        $('.field-purchasingaccountcharge-cash_back_credit_card_rate').hide();

        if (accountType == 'credit-card') {
            $('.field-purchasingaccountcharge-cash_back_credit_card_rate').show();
        }
    }

    $(document).ready(function () {
        showHideFunction();

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
    });


</script>
