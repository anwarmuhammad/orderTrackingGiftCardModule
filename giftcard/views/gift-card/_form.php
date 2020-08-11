<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\jui\DatePicker;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\core\buttons\FormButtons;
use unlock\modules\core\buttons\BackButton;
use unlock\modules\supplier\models\Supplier;
use unlock\modules\supplier\models\GiftCardSeller;
use unlock\modules\giftcard\models\GiftCard;
use yii\bootstrap\Modal;

$isNewRecord = $model->isNewRecord;

/* @var $this yii\web\View */
/* @var $model unlock\modules\giftcard\models\GiftCard */
/* @var $form yii\bootstrap\ActiveForm */

$this->title= ($model->isNewRecord) ? Yii::t('app', 'Create Gift Card') : Yii::t('app', 'Update Gift Card');
?>
<style>

    /*------------Add New Suppler Sections----------------*/

    #giftcard-supplier_id option[value="0"] {
        position: relative;
        color: #1abc9c;
    }

    #newSupplierModalContent .modal-header {
        padding: 10px !important;
    }
    #newSupplierModalContent .modal-title {
        display: block!important;
    }

    /*------------End Add New Suppler Sections----------------*/

    .modal-header {
        padding: 1rem 0rem;
        border-bottom: 0px solid #fff!important;
        /* background-color: #3C4B48; */
    }

    .gift-card-content{
        margin-bottom: 10px;
    }

    .form-control-icon{
        float: right;
        margin-top: -27px;
        margin-right: 5px;
    }

    a, p, h4 {
    color: #B2B2B2;
   }

    #gift-card-form .gift-card-content .field-gift_card_seller_account_id{
        display: none;
    }

    .control-label {
        margin-bottom: 0;
        font-family: "Kanit-Light";
    }
    #billing-create-form input,
    #billing-create-form textarea{
        color: #888888;.
    padding-left: 5px!important;
    }
    .modal-body{
        padding-top: 0;
    }
    .modal-header {
        border: none!important;
        padding: 10px 20px!important;
    }
    .modal-title {
        color: #4E4E4E!important;
        font-family: "Kanit-SemiBold";
        padding-left: 15px;
    }
    .modal-header button.close {
        position: absolute!important;
        top: 31px!important;
        z-index: 5;
        right: 47px;
    }
    .modal-dialog-scrollable .modal-body{
        height: 100%!important;
        width: 99%;
        margin: 0 auto;
    }
    /*.modal-content{*/
        /*height: 100%!important;*/
    /*}*/

    .modal-content input, .modal-content textarea, .modal-content select {
        color: #888888;
        padding: 1px 5px;
    }

    .field-giftcard-doc_id{
        color: #4E4E4E!important;
        font-family: "Kanit-SemiBold";
        padding-left: 15px;
        font-size:20px;
        line-height: 1.2;
    }
    .show-pass-gift{
        position: absolute;
        right: 8px;
        top: 3px;
        z-index: 5;
    }
    #show_hide_password{
        position: relative;
    }
    #giftcard-gift_card_pin{
        border-radius: .25rem;
    }

    .add-another-btn .btn-add-more{
        position: relative;
        padding: 0;
    }
    .help-block-error {  display: none !important;  }
    .payment-account-modal-title{
        display: none;
    }
    #giftCardTransactionModalContent .payment-account-modal-title{
        display: block;
    }
    .modal-title span {
        color: #B2B2B2;
    }
    .card-security-sign {
        position: absolute!important;
        top: 27px!important;
        left: 20px!important;
        color: #888888!important;
        font-family: "Kanit-SemiBold";
    }
    #giftcard-gift_card_number{
        padding-left: 43px;
    }
    input#giftcard-price {
        padding-left: 15px;
    }

    .psub2-total .amount-icon {
        left: 9px!important;
        top: 3px!important;
    }
    input#giftcard-price , input#giftcard-initial_balance{
        padding-left: 22px;
    }
</style>
<div class="main-container main-container-form" role="main">
    
    <div class="admin-form-container">
        <div class="panel panel-default">
            
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',  //  'default', 'horizontal' or 'inline'
                    'id' => 'gift-card-form',
                    /*'options' => ['enctype' => 'multipart/form-data'],*/
                    'validateOnBlur' =>true,
                    'enableAjaxValidation'=>false,
                    'errorCssClass'=>'has-error',
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\" \">\n{input}\n{hint}\n{error}\n</div>",
                        'options' => ['class' => 'form-group'],
                        'horizontalCssClasses' => [
                            'label' => '',
                            'offset' => '',
                            'wrapper' => '',
                            'hint' => '',
                        ],
                    ],
                ]); ?>

             <div class="modal-body">
             <div class="input-group form-control border-0">

             <h4 class="gift-card-modal-title" style="font-size:20px;"> Gift Card</h4>
                <?php if(!$isNewRecord) { ?>
                    <?= $form->field($model, 'doc_id', ['inputTemplate' => $model->doc_id])->hiddenInput()->label(false) ?>
                     <?php } ?>
              </div>
                <div id="box-payment-method" style="overflow: hidden; height: calc(100vh - 300px); border: 1px solid #8c368c; margin: 0 15px; padding: 20px; border-radius: 3px; margin-bottom: 20px; margin: 0 auto;">

                <div class="row" style="overflow-y: auto; max-height: calc(100vh - 325px);">
                <div class="<?php if($isNewRecord || !$hasMatchingRuleItem) { ?> col-md-6 <?php } else {?> col-md-4 <?php } ?>">

                <div class="gift-card-content">
                <?= $form->field($model, 'gift_card_number')->textInput(['maxlength' => true])->label('Account<span class="text-danger">*</span> <small style="font-size: 10px;">"Enter last 4 digits"</small>') ?>
                <span class="card-security-sign">******</span>
                </div>

                <!-- <div class="gift-card-content">

                <?php //$form->field($model, 'gift_card_pin',['template' => "{label}\n<div class=\"\"><div class=\"input-group\" id=\"show_hide_password\">\n{input}<div class=\"input-group-addon show-pass-gift\"><a href=\"\"><svg xmlns=\"http://www.w3.org/2000/svg\" width=\"17.002\" height=\"8.889\" viewBox=\"0 0 17.002 8.889\"><g transform=\"translate(-354.676 -303.111)\"><path d=\"M3.029,0A3.029,3.029,0,1,1,0,3.029,3.029,3.029,0,0,1,3.029,0Z\" transform=\"translate(360 305.942)\" fill=\"#ccc\"/><path d=\"M-62,237.6s6.817-9.929,15.422,0\" transform=\"translate(417.5 70.954)\" fill=\"none\" stroke=\"#ccc\" stroke-width=\"2\"/></g></svg></a></div>\n{hint}\n{error}\n</div></div>"])->passwordInput(['maxlength' => true])->label('Pin') ?>
                </div> -->


                <div class="gift-card-content">
                    <?= $form->field($model, 'supplier_id')->dropDownList(['0' => '+ Add New'] + Supplier::supplierDropDownList2($model['supplier_id']),['prompt'=>'Select '.$model->getAttributeLabel('supplier_id')]) ?>
                </div>

                <div class="gift-card-content">
                <?= $form->field($model, 'gift_card_seller_id')->dropDownList(GiftCardSeller::giftCardSellerDropDownList2($model['gift_card_seller_id']),['prompt'=>'Select '.$model->getAttributeLabel('gift_card_seller_id'), 'id' => 'dep_drop_seller'])->label('Gift Card Seller'); ?>
                </div>

                <input type="hidden" id="gift_card_seller_edited_id" value="<?= $model->gift_card_number ?>">

                <div class="gift-card-content">
                    
                <?php
                if (empty($model->gift_card_seller_account_id)) {
                    echo $form->field($model, 'gift_card_seller_account_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                        'options' => ['id' => 'gift_card_seller_account_id'],
                        'pluginOptions' => [
                            'depends' => ['dep_drop_seller'],
                            'placeholder' => 'Select ' . $model->getAttributeLabel('gift_card_seller_account_id'),
                            'url' => Url::to(['/ajax/ajax/seller-account-dep-drop-down-list'])
                        ]
                    ])->label('Gift Card Seller Account');
                } else {
                    echo $form->field($model, 'gift_card_seller_account_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                        'data' => [$model->gift_card_seller_account_id => 'default'],
                        'options' => ['id' => 'gift_card_seller_account_id'],
                        'pluginOptions' => [
                            'depends' => ['dep_drop_seller'],
                            'initialize' => true,
                            'placeholder' => 'Select ' . $model->getAttributeLabel('gift_card_seller_account_id'),
                            'url' => Url::to(['/ajax/ajax/seller-account-dep-drop-down-list'])
                        ]
                    ])->label('Gift Card Seller Account');
                }
                ?>
                  </div>
                </div>

                <div class="<?php if($isNewRecord || !$hasMatchingRuleItem) { ?> col-md-6 <?php } else {?> col-md-4 <?php } ?>" style="position: relative;">

                <div class="gift-card-content">
                <?= $form->field($model,'purchased_date')->widget(\yii\jui\DatePicker::className(),
                [
                    'options' => ['class' => 'form-control', 'readonly'=>true],
                    'language' => 'en-GB',
                    'value'  => $model->purchased_date,
                    'dateFormat' => 'MM/dd/yyyy',
                    'clientOptions' => [
                        'changeYear' => true,
                        'changeMonth' => true,
                        'todayHighlight' => true,
                    ],
                ])->label('Purchase Date') ?>
                    <i class="calender-gift-purchased-date fas fa-calendar-alt" style="color: #B2B2B2; position: absolute; right: 20px; top: 28px;"></i>
                
                </div>

                <div class="gift-card-content">
                <div class="row">
                    <div class="col-md-6">
                          <?= $form->field($model, 'initial_balance', ['template' => '{label}<div class="nopadding"><div class="psub2-total"><span class="amount-icon">$</span>{input}{error}</div></div>'])->textInput(['maxlength' => true])->label('Initial Balance') ?>
                    </div>
                    <div class="col-md-6" style="position: relative;">
                            <div style="width: 85%; display: inline-block; margin-right: 5px;">
                                <?= $form->field($model,'initial_balance_date')->widget(\yii\jui\DatePicker::className(),
                                    [
                                        'options' => ['class' => 'form-control', 'readonly'=>true],
                                        'language' => 'en-GB',
                                        'value'  => $model->initial_balance_date,
                                        'dateFormat' => 'MM/dd/yyyy',
                                        'clientOptions' => [
                                            'changeYear' => true,
                                            'changeMonth' => true,
                                            'todayHighlight' => true,
                                        ],
                                    ])->label('&nbsp;') ?>
                            </div>
                        <i class="calender-gift-initial-balance fas fa-calendar-alt" style="color: #B2B2B2; position: relative; top: -1px;"></i>
                    </div>
                </div>
                </div>

            
               <div class="gift-card-content">
                   <?= $form->field($model, 'price', ['template' => '{label}<div class="nopadding"><div class="psub2-total"><span class="amount-icon">$</span>{input}{error}</div></div>'])->textInput(['maxlength' => true]);?>
               </div>

               <div class="gift-card-content">
               <?= $form->field($model, 'value')->textInput(['maxlength' => true])->label('Value') ?>
               </div>
                
               <div class="gift-card-content">
               <?= $form->field($model, 'discount_rate')->textInput(['maxlength' => true,'readonly'=>true])->label('Discount') ?>
              </div>

             </div>
                <?php if(!$isNewRecord && $hasMatchingRuleItem) { ?>
                    <div class="col-md-4">

                    <div class="matching-rule-container" id="dynamic-multiple-matching-rule">
                    <!-- <div class="gift-card-content">
                  
                    <label class="col-sm-12"><p>MATCHING RULES</p></label>

                    </div> -->
                    <div class="matching-rule-content">
                        <table class="table table-no-bordered">
                         <h5 style="text-transform: uppercase; color: #888888;">MATCHING RULES</h5>
                            <?php foreach ($modelMatchingRuleItems as $index => $item) { ?>

                                <?php 
                                    
                                    //$giftCardId = GiftCard::getGiftCardIdByNumber($item->match_text);

                                    //$item->match_text = $giftCardId;
                                ?>
                                
                                <?php if ($hasMatchingRuleItem) { ?>
                                    <tr>
                                        <!-- <td class="col-sm-12">Account <span class="serial-number"><?= $index + 1 ?></span></td> -->
                                        <td style="padding: 0; width: 100%; padding-right: 5px; vertical-align: middle;">
                                            <span class="control-label">Account <?= $index + 1 ?></span>
                                           <?= $form->field($item, "[$index]id", ['template' => '{input}', 'options' => ['tag' => null]])->hiddenInput(['class' => 'field-id']); ?>
                                           <?php /* $form->field($item, "[$index]match_text")->dropDownList(GiftCard::giftCardDropDownList(),['prompt' => 'Select Gift Card'])->label(false) */ ?>
                                            <?= $form->field($item, "[$index]match_text", ['template' => '{input}'])->textInput(['maxlength' => true, 'class' => 'form-control field-match-text']) ?>

                                           
                                        </td>
                                        <td style="padding:0;">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <a href="javascript:void(0)" class="remove-matching-rule-row" id="delete-matching-rule-ids"
                                                    data-attr-id="<?= $index ?>"><img  src="<?= Yii::getAlias('@web/themes/espiralo') ?>/images/delete.png"/> &nbsp;</a>
                                                </div>
                                               
                                     
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </table>

                        <div class="add-another-btn" style="padding:0;">
                            <!-- <a href="javascript:void(0)" class="btn btn-add-more btn-add-new-matching-rule-row" id="add-new-account-row">
                                <i class="fa fa-plus-circle"></i> &nbsp;ADD NEW
                            </a> -->

                            <a href="javascript:void(0)"
                                           class="btn btn-add-more add-new-payment-row action-add-visual-feature btn-add-new-matching-rule-row"
                                           id="add-new-payment-row">
                                            <i class="fa fa-plus-circle add-another"></i> &nbsp;Add New
                             </a>
                        </div>

                        <!-- <tfoot>
                                <tr>
                                    <th>
                                        <a href="javascript:void(0)"
                                           class="btn btn-add-more add-new-payment-row action-add-visual-feature"
                                           id="add-new-payment-row">
                                            <i class="fa fa-plus-circle add-another"></i> &nbsp;Add Another
                                        </a>
                                    </th>
                                </tr>
                        </tfoot> -->
                    </div>
                </div>

                    </div>
                    <?php } ?>
                </div>
            </div>
                

                <div class="admin-form-button" style="padding-top: 20px;">
                    <?= $form->field($model, 'org_id')->hiddenInput(['value'=> CommonHelper::getLoggedInUserOrgId()])->label(false) ?>
                    <div id="form-submit-error-message" class="alert alert-success alert-dismissable"  style= "display:none" >
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-check"></i>Saved!</h4>
                                
                    </div>
                    
                    <div class="form-group">
                        <div class=" float-right">
                        <?php
                        if($model->doc_id){
                            echo  Html::button('Cancel', ['id' => 'giftcard-cancel-view', 'value' =>   Url::toRoute(['/gift-card/gift-card/view', 'docId' => $model->doc_id]), 'class' => 'btn btn-success', 'style' =>'']);
                        }
                        ?>

                        <?= FormButtons::widget(['item' => ['save'], 'saveClassName' => 'saveGiftCardForm','docId' => $model->doc_id]) ?>
                        </div>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>
</div>

<div class="hidden-field-for-javascript" style="display:none">
    <?= Html::dropDownList('gift_Card_number_hidden_field', null, GiftCard::giftCardDropDownList(), ['prompt' => 'Select Gift Card', 'id' => 'hidden-drop-down-field-gift-card-number']) ?>
</div>
<?php
$baseUrl=Yii::getAlias('@baseUrl');
Modal::begin([
    'header' => '<h4 class="modal-title billingUpdate" id="header-content">Add Gift Card Seller</h4>',
    'id'     => 'newGiftCardSellerModalContent',
    'size'   => 'modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered',
    'closeButton' => [
        'id'=>'close-button-n-two',
        'class'=>'modal-in-close-btn',
        'data-dismiss' =>'modal5',
    ],
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
]);
echo "<div id='add-newGiftCardSellerModalContent'></div>";
Modal::end();

// Cancel Create Modal Popup
Modal::begin([
    'header' => '<h4 class="modal-title dynamic-title" id="header-content"></h4>',
    'id'     => 'giftCardTransactionModalContent',
    'size'   => 'modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered',
    'closeButton' => [
        'id'=>'close-button-transaction',
        'class'=>'modal-in-close-btn',
        'data-dismiss' =>'modal5',
    ],
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
]);

echo "<div id='add-giftCardTransactionModalContent'></div>";

Modal::end();

Modal::begin([
    'header' => '<h4 class="modal-title" id="supplier-header-content"></h4>',
    'id'     => 'newSupplierModalContent',
    'size'   => 'modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered',
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
        $('#giftcard-cancel-view').click(function(){
            $("#inbox-content-view").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");
            var docId = '<?php echo $model->doc_id; ?>';
            $.ajax({
                url: '<?php echo Yii::$app->request->baseUrl . '/gift-card/gift-card/update?docId=' ?>' + docId,
                success: function (html) {
                    $("#inbox-content-view").empty().append(html);
                    //$("#nav li").removeClass('isDisabled');
                    //$(".modal-content .stepper").removeClass('docnavoff');
                }, beforeSend: function (xhr) {
                    //$("#nav li").addClass('isDisabled');
                    //$(".modal-content .stepper").addClass('docnavoff');
                },
            });
        });
    });

    $(document).on('click', '#kanban-button-create-charge', function () {

        $("#add-giftCardTransactionModalContent").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");
        $("h4.modal-title.dynamic-title").html("<span class='payment-account-modal-title'>"+"Charge"+"</span>");
        $('#giftCardTransactionModalContent').modal('show')
            .find('#add-giftCardTransactionModalContent')
            .load($(this).attr('value'));

    });

    $(document).on('click', '#kanban-button-create-pay', function () {

        $("#add-giftCardTransactionModalContent").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");
        $('#giftCardTransactionModalContent').modal('show')
            .find('#add-giftCardTransactionModalContent')
            .load($(this).attr('value'));
        $("h4.modal-title.dynamic-title").html("<span class='payment-account-modal-title'>"+"Pay Gift Card"+"</span>");

        //$("#kanban-button-create-payment").trigger('click');


    });

    $(document).on('click', '#kanban-button-create-reload', function () {

        $("#add-giftCardTransactionModalContent").empty().append("<div id='loading'><img src='<?php echo Yii::getAlias('@baseUrl');?>/backend/web/themes/espiralo/images/loader.gif' alt='Loading' /></div>");
        $("h4.modal-title.dynamic-title").html("<span class='payment-account-modal-title'>"+"Reload"+"</span>");
        $('#giftCardTransactionModalContent').modal('show')
            .find('#add-giftCardTransactionModalContent')
            .load($(this).attr('value'));

    });

    $('#close-button-transaction').click(function(){
        $('#giftCardTransactionModalContent').modal('hide');
        reloadMainPopupAreaFunc();
    });

    $(document).on('change', '#dep_drop_seller', function () {
        var supplierAccount = $(this).val();
        if (supplierAccount == 0)
        {
            var loaded_url = '<?= Url::toRoute(['/supplier/gift-card-seller/create?createForm=gift-card']) ?>';

            $('#newGiftCardSellerModalContent').modal('show')
                .find('#add-newGiftCardSellerModalContent')
                .load(loaded_url);
        }
        $('#gift-card-form .gift-card-content .field-gift_card_seller_account_id').show();
    });
    $(document).on('click', '#close-button-n-two', function () {
        $('#newGiftCardSellerModalContent').modal('hide');

        if($("#gift_card_seller_edited_id").val()) {
            reloadMainPopupAreaFunc();
        }else{
            reloadMainPopupDropdownAreaFunc();
        }

    });

    $(document).ready(function () {

        $('#dep_drop_seller option:first').after($('<option />', { "value": '0', text: '-- Add New --', class: 'add_new' }));
        //Calculate Gift Card Discount

      calculateGiftCardDiscount();

      $(document).on('keyup', '#giftcard-price, #giftcard-value', function () {
            calculateGiftCardDiscount();
        });

        function calculateGiftCardDiscount() {

        var price = parseFloat($('#giftcard-price').val());
        var value = parseFloat($('#giftcard-value').val());

        if(value && price ){

           var discount = (100*(value-price))/value;
        }

        if(!isNaN(discount) && discount >=0){
            $("#giftcard-discount_rate").val(discount.toFixed(2)+"%");

        }
        
    }
     
        var deleteMatchingRuleIds = [];

        // Add New Row
        var index = $('#dynamic-multiple-matching-rule tbody tr').length;

        $(document).off('click', '.remove-matching-rule-row').on('click', '.remove-matching-rule-row', function () {
            var confirmResult = confirm("Are you sure you want to delete this item?");

            if (confirmResult) {
                var matchingRuleId = $(this).closest("tr").find('.field-id').val();
               
                if (matchingRuleId != '') {
                    $.ajax({
                        url: '<?php echo Yii::$app->request->baseUrl . '/ajax/ajax/delete-gift-card-matching-rule' ?>',
                        type: 'POST',
                        data: {
                            matchingRuleId: matchingRuleId,
                            _csrf: '<?=Yii::$app->request->getCsrfToken()?>'
                        },
                        success: function (data) {

                        }
                    });

                }

                $(this).closest("tr").remove();
            }
        });


        // Add Dynamic Row
        $('.btn-add-new-matching-rule-row').on('click', function () {
            var rowHtmlPayment = prepareRow(index);
            $('#dynamic-multiple-matching-rule tbody').append(rowHtmlPayment);
            index++;

            setSerialNumber();
        })

    });



    function prepareRow(index) {
        
        var giftCardNumberList = $('#hidden-drop-down-field-gift-card-number').html();
    
        var html = '';

        html += '<tr>';

        html += '<td style="padding: 0; width: 100%; padding-right: 5px; padding-bottom: 15px;">' +
                '<span class="control-label">Account ' + (index + 1) +'</span>' +
                '<div class="input-group form-control' + index + '-match_text">' +
                    '<input id="matchingrule-' + index + '-match_text" class="form-control field-account" name="MatchingRule[' + index + '][match_text]" aria-invalid="true"></input>' +
                '</div>' +
                '<input type="hidden" id="matchingrule-' + index + '-id" class="field-id" name="MatchingRule[' + index + '][id]" value="">'
            '</td>';
       

          // Delete Button
         html += '<td style="padding:0;">' +
                '<label style="margin:0;">&nbsp;</label>'+
                '<div>' +
                '<a href="javascript:void(0)" class="remove-matching-rule-row" data-attr-id=' + index + '><img  src="<?= Yii::getAlias('@web/themes/espiralo') ?>/images/delete.png"/></a>' +
                '</div>' +
                '</td>';
        html += '</tr>';

        return html;
    }

    function addDynamicValidation(index, fieldName, errorMessage) {
        $('#formName').yiiActiveForm('add', {
            id: 'matchingrule-' + index + '-' + fieldName,
            name: '[' + index + ']' + fieldName,
            container: '.field-match-text-' + index + '-' + fieldName,
            input: '#matchingrule-' + index + '-' + fieldName,
            error: '.help-block.help-block-error',
            validate: function (attribute, value, messages, deferred, $form) {
                yii.validation.required(value, messages, {message: errorMessage});
            }
        });
    }

    function setSerialNumber() {
        var count = 1;
        $(".serial-number").each(function( index, element ) {
            $(this).text(count);
            count++;
        });
    }

  
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if($('#show_hide_password input').attr("type") == "text"){
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass( "fa-eye-slash" );
                $('#show_hide_password i').removeClass( "fa-eye" );
            }else if($('#show_hide_password input').attr("type") == "password"){
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass( "fa-eye-slash" );
                $('#show_hide_password i').addClass( "fa-eye" );
            }
        });

       

</script>

<script>


$(document).ready(function() {
    //supplier id auto selection only for -- Add -- 
    var supplier_id_for_ngc = $('#supplier-id-for-ngc').data('supplier-id');
    //console.log('supplier-id-for-ngc: '+supplier_id_for_ngc);
    if(supplier_id_for_ngc)
    {
        //console.log('supplier id selection');
        $('#giftcard-supplier_id').val(supplier_id_for_ngc);
    }
    

    $('.saveAjax').off().on('click', function () {

        $('.help-block-error').attr('style', 'display:block !important;');
        
        $('#gift-card-form').on('beforeSubmit', function(e) {
    //$(".saveGiftCardForm").unbind().on("click", function(e) {

            var purchaseForm = $("#gift-card-form");

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
                    if(data.created == "giftcardEditDoc"){
                        var gcIndex = '<?= Url::toRoute(['index'])?>';
                        $("#form-submit-error-message").html("Successfully Saved Data.");
                        $("#form-submit-error-message").attr("style", "color:green");
                        //
                        // setTimeout(function(){// wait for 5 secs(2)
                        //     //location.reload(gcIndex); // then reload the page.(3)
                        //     //$('#giftCardModal').modal('hide');
                        //     //reloadMainFilterFunc();
                        //
                        //     reloadGiftCardWebIndex();
                        // }, 1000)

                        var docId =data.doc_id;
                        $('#giftCardModal').modal('hide');

                        var loadUrl = '<?= Url::toRoute(['/gift-card/gift-card/update'])?>?docId=' + docId;

                        setTimeout(function(){// wait for 5 secs(2)
                            $('#inboxModal').modal('show')
                                .find('#inbox-content-view')
                                .load(loadUrl);
                        }, 1000);

                    } else if(data.createFrom == "purchaseOrderPayment") {

                        $("#form-submit-error-message").html("Successfully Saved Data.");
                        $("#form-submit-error-message").attr("style", "color:green");
                        // parentThis.closest('tr').find('.payment-method-account').append('<option value="'+data.giftCardId+'"  data-cash-back-rate="'+data.giftCardDiscount+'" selected>'+ data.giftCardNumber +'</option>');
                        $("#"+data.dropDownId+"").append('<option value="'+data.giftCardId+'"  data-cash-back-rate="'+data.giftCardDiscount+'" selected>'+ data.giftCardNumber +'</option>');



                        setTimeout(function () {// wait for 5 secs(2)

                            $("#newGiftCardModalContent").modal('hide');

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

        }).on('submit', function(e){

            e.preventDefault();

        });

    })
});


</script>

<script>
    $(document).ready(function() {
        $("#giftcard-initial_balance_date").datepicker();
        $('.calender-gift-initial-balance').click(function() {
            $("#giftcard-initial_balance_date").focus();
        });
    });

    $(document).ready(function() {
        $("#giftcard-purchased_date").datepicker();
        $('.calender-gift-purchased-date').click(function() {
            $("#giftcard-purchased_date").focus();
        });
    

    //BEGIN card no 4 digit dev
    $('#giftcard-gift_card_number').off('keyup', '#giftcard-gift_card_number').keyup(function() {
        var account = $(this).val();
        account = account.replace(/\D/g, '');      // remove all occurrences except numbers
        $(this).val(account);

        if(account.length > 4) {
            $(this).val($(this).val().slice(0, 4));
        }
    });

     //END card no 4 digit dev

});

//Add new Supplier Sections Create
$(document).on('change', '#giftcard-supplier_id', function () {
    var selectedGiftCardValue  = $(this).val();
    if(selectedGiftCardValue == 0){
        var createFrom = 'giftCard';
        var loadSupplierUrl = '<?= Url::toRoute(['/supplier/supplier-summary/create'])?>?createFrom=' + createFrom;
        $('#supplier-header-content').html('Add New Supplier');
        $('#newSupplierModalContent').modal('show')
            .find('#add-newSupplierModalContent')
            .load(loadSupplierUrl);
    }
});

$('#close-button-supplier-create').click(function(){
    $('#newSupplierModalContent').modal('hide');
    $('#giftcard-supplier_id').prop('selectedIndex',0);
});

</script>

