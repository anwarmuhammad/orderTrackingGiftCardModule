<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use unlock\modules\core\buttons\ExportCsvButton;
use unlock\modules\core\buttons\ExportPdfButton;
use unlock\modules\core\buttons\ResetFilterButton;
use unlock\modules\core\buttons\SearchFilterButton;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\supplier\models\SupplierAccount;
use unlock\modules\cashback\models\CashBackWebsite;
use unlock\modules\supplier\models\Supplier;
use unlock\modules\supplier\models\GiftCardSeller;
use unlock\modules\giftcard\models\GiftCard;

/* @var $this yii\web\View */
/* @var $model unlock\modules\supplier\models\SupplierSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<style>
    /*search filter design start*/
    .search-text li {
        text-align: left;
        padding: 0;
        width: auto;
        display: inline-block;
    }
    .search-text{
        margin-bottom: 20px;
    }
    ul.search-text li:nth-child(2) a {
        position: absolute;
        left:220px;
    }
    ul.search-text li:nth-child(3) a {
        position: absolute;
        left: 340px;
    }
    ul.search-text li:nth-child(4) a {
        position: absolute;
        left:455px;
    }
    ul.search-text li:nth-child(5) a {
        position: absolute;
        left:575px;
    }
    ul.search-text li:nth-child(6) a {
        position: absolute;
        left:750px;
    }
    .search-date-filter {
        position: unset;
    }
    .search-text li > div{
        position: relative;
        left: -200px !important;
        bottom: -35px;
    }
    ul.search-text li:nth-child(2)  > div{ left: -198px !important; }
    ul.search-text li:nth-child(3)  > div{ left: -212px !important; }
    ul.search-text li:nth-child(4)  > div{ left: -227px !important;  }
    ul.search-text li:nth-child(5)  > div{ left: -240px !important;  }
    ul.search-text li:nth-child(6)  > div{ left: -252px !important;  }
    .search-text li .select2-container--default{
        width:auto !important;
    }
    .search-text .selection .select2-selection--multiple {
        width: 160px;
        z-index: 5;
    }
    .search-text .selection .select2-selection--multiple .select2-selection__rendered li {
        overflow: hidden;
        width: auto;
        display: inline-block;
        /*max-width: 150px;*/
    }
    .select2-container--open input.select2-search__field {
        width: 85% !important;
    }
    .search-text .selection .select2-selection--multiple {
        width: auto;
        z-index: 5;
    }
    /*search filter design end*/
    .select2-dropdown {
        border: 0px solid #aaa;
    }
    ul.search-text li .field-status{
        left: -365px !important;
        bottom: -35px !important;
    }
    /*search filter new design end*/
    .search-text .selection .select2-selection--multiple .select2-selection__rendered li.select2-search--inline { max-width: 300px; }
    .select2-container--open input.select2-search__field {  width: 300px !important;padding: 2px 6px !important; }
    .select2-container--default .select2-selection--multiple .select2-selection__rendered { padding-right: 10px; }
    .date-filter-search {left: 460px !important;}
    .select2-dropdown {border: 0px solid #aaa;}

    #select2-gift-card-filter-results {left: 323px;position: fixed; top: 162px; z-index: 999;} .field-gift-card-filter .select2-search--inline{left: 323px;position: fixed; top: 130px; z-index: 999;}
    #select2-supplier-name-results {left: 445px;position: fixed; top: 162px; z-index: 999;} .field-supplier-name .select2-search--inline{left: 445px;position: fixed; top: 130px; z-index: 999;}
    #select2-giftcard-seller-results {left: 562px;position: fixed; top: 162px; z-index: 999;} .field-giftcard-seller .select2-search--inline{left: 562px;position: fixed; top: 130px; z-index: 999;}
    #select2-giftcard-seller-account-results {left: 680px;position: fixed; top: 162px; z-index: 999;} .field-giftcard-seller-account .select2-search--inline{left: 680px;position: fixed; top: 130px; z-index: 999;}
    #select2-status-results {left: 855px;position: fixed; top: 162px; z-index: 999;} .field-status .select2-search--inline{left: 855px;position: fixed; top: 130px; z-index: 999;}

</style>

<div class="admin-search-form-container product-search">
    <?php $form = ActiveForm::begin([
        'action' => ['ajax-search-index'],
        'method' => 'POST',
        'id'    => 'searchfilter',
        'layout' => 'horizontal',  //  'default', 'horizontal' or 'inline'
        'validateOnBlur' => false,
        'enableAjaxValidation' => false,
        'options' => ['data-pjax' => true ],
        'errorCssClass'=>'has-error',
        'fieldConfig' => [
            'template' => "{input}",
            'options' => [],
            'horizontalCssClasses' => [
                'label' => '',
                'offset' => '',
                'wrapper' => '',
                'hint' => '',
            ],
        ],
    ]); ?>

    <div class="admin-search-form-fields">
        <div class="row">
            <div class="col-12">
                <ul class="search-text">
                    <li style="width:190px; position: relative;  top: -16px; margin-right:0px;text-align: left;padding-left: 0">
                        <span class="panel-title"><?= Html::encode($this->title) ?></span>
                        <div class="search-filter-view">
                            <ul>

                            </ul>
                            <span class="search-filter-closeicon">×</span>
                        </div>
                    </li>
                    <li>
                        <a for="gift-card-filter" class="gift-card-filter-label" role="tab" aria-selected="true">Gift Card <i class="fas fa-sort-down custom-down-arrow"></i></a>
                        <?= $form->field($model, 'gift_card_number[]')->dropDownList(GiftCard::allGiftCardDropDownList(), ['id' => 'gift-card-filter', 'class' => 'form-control select2', 'multiple' => '']) ?>
                    </li>
                    <li>
                        <a for="supplier-name" class="supplier-label" role="tab" aria-selected="true">Supplier <i class="fas fa-sort-down custom-down-arrow"></i></a>
                        <?= $form->field($model, 'supplier_id[]')->dropDownList(Supplier::supplierDropDownList(), ['id' => 'supplier-name', 'class' => 'form-control select2', 'multiple' => '']) ?>
                    </li>
                    <li>
                        <a for="giftcard-seller" class="giftcard-seller-label" role="tab" aria-selected="true">Seller <i class="fas fa-sort-down custom-down-arrow"></i></a>
                        <?= $form->field($model, 'gift_card_seller_id[]')->dropDownList(GiftCardSeller::giftCardSellerDropDownList(), ['id' => 'giftcard-seller', 'class' => 'form-control select2', 'multiple' => '']) ?>
                    </li>
                    <li>
                        <a for="giftcard-seller-account" class="giftcard-seller-account-label" role="tab" aria-selected="true">Seller Account <i class="fas fa-sort-down custom-down-arrow"></i></a>
                        <?= $form->field($model, 'gift_card_seller_account_id[]')->dropDownList(SupplierAccount::supplierAccountDropDownList(), ['id' => 'giftcard-seller-account', 'class' => 'form-control select2', 'multiple' => '']) ?>
                    </li>
                    <li>
                        <a for="status" class="status-label" role="tab" aria-selected="true">Status <i class="fas fa-sort-down custom-down-arrow"></i></a>
                        <?= $form->field($model, 'status[]')->dropDownList([1 => 'Active', 2 => 'Inactive'], ['id' => 'status', 'class' => 'form-control select2', 'multiple' => '']) ?>
                    </li>
                    <!--<li>
                        <?php /*
                        <?= $form->field($model, 'balance[]')->dropDownList(['0' => 'Zero', '1' => 'Available'], ['prompt' => 'Balance', 'id' => 'balance-name', 'class' => 'form-control', 'style' => 'display:block;border: none;']) ?>
                        */ ?>
                    </li>-->
                </ul>

            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    $(document).ready(function () {
        $('.select2').select2();
        $(".gift-card-filter-label").on("click", function () {
            $('.field-gift-card-filter .select2').select2('open');
        });
        $(".supplier-label").on("click", function () {
            $('.field-supplier-name .select2').select2('open');
        });
        $(".giftcard-seller-label").on("click", function () {
            $('.field-giftcard-seller .select2').select2('open');
        });
        $(".giftcard-seller-account-label").on("click", function () {
            $('.field-giftcard-seller-account .select2').select2('open');
        });

        $(".balance-label").on("click", function () {
            $('.field-balance-name .select2').select2('open');
        });
        $('.search-filter-view .search-filter-closeicon').on('click', function () {
            $('.search-filter-view').hide();
            //$("#balance-name option:first").attr('selected','selected');
        });
        $(".status-label").on("click", function () {
            $('.field-status .select2').select2('open');
        });
        $('#gift-card-filter, #supplier-name, #giftcard-seller, #giftcard-seller-account, #balance-name, #status').on('change', function () {

            var balanceName = $('#balance-name').val();
            if(balanceName == 0){
                $('.search-filter-view .search-filter-closeicon').show();
                $('.search-filter-view').show();
                $('.search-filter-view ul').html('<li>Zero</li>');
            }if(balanceName == 1){
                $('.search-filter-view .search-filter-closeicon').show();
                $('.search-filter-view').show();
                $('.search-filter-view ul').html('<li>Available</li>');
            }
            if(!balanceName){
                $('.search-filter-view .search-filter-closeicon').hide();
                $('.search-filter-view ul').html('');
            }

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

        })
    });


    function reloadGiftCardWebIndex() {

        $(".after-select-kanban-button").hide();

        var url = '<?php echo Url::toRoute(['/gift-card/gift-card']); ?>';
        console.log('url '+url);

        $.ajax({
            url: url,
            type: "POST",
            data: '',
            dataType: 'html',
            success: function(data){
                console.log('data '+data);
                $("body").html(data);
            }
        });

    }

</script>

