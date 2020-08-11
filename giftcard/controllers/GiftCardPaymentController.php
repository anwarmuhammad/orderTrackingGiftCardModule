<?php

namespace unlock\modules\giftcard\controllers;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use unlock\modules\giftcard\models\GiftCard;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\core\helpers\CostCalculation;
use unlock\modules\giftcard\models\GiftCardPayment;
use unlock\modules\purchaseorder\models\Transaction;
use unlock\modules\purchaseorder\models\PurchaseOrder;
use unlock\modules\cashback\models\TransactionCashBack;
use unlock\modules\giftcard\models\GiftCardPaymentItem;
use unlock\modules\paymentmethod\models\TransactionPaymentMethod;
use unlock\modules\purchasingaccount\models\PurchasingAccountCharge;


/**
 * PurchaseOrderPaymentController implements the CRUD actions for PurchaseOrderInvoice model.
 */
class GiftCardPaymentController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => Yii::$app->DynamicAccessRules->customAccess('purchasingaccount/purchasingaccountcharge'),
        ];
    }

    /**
     * Creates a new PurchaseOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($refDocId=null)
    {
        $model = new GiftCardPayment();
        $model->gift_card_pay_date = date("Y-m-d");

        // Gift Card Item
        $hasGiftCardPaymentItem = true;
        $modelGiftCardPaymentItems = [new GiftCardPaymentItem()];

        $giftCardItemsArray = Yii::$app->request->post('GiftCardPaymentItem');
        if($giftCardItemsArray){
             $modelGiftCardPaymentItems = [];
             foreach ($giftCardItemsArray as $key => $item) {
                 $modelGiftCardPaymentItems[$key] = new GiftCardPaymentItem();
             }
        }

        // Payment Method
        $hasPaymentMethodItem = true;
        $modelPaymentMethodItems = [new Transaction()];

        $paymentMethodItemsArray = Yii::$app->request->post('Transaction');
        if($paymentMethodItemsArray){
            $modelPaymentMethodItems = [];
            foreach ($paymentMethodItemsArray as $key => $item) {
                $modelPaymentMethodItems[$key] = new Transaction();
            }
        }

        // Validation
        $model->load(Yii::$app->request->post());
        Model::loadMultiple($modelGiftCardPaymentItems, Yii::$app->request->post());
        Model::loadMultiple($modelPaymentMethodItems, Yii::$app->request->post());
//        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
//            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//            $validationError = array_merge(
//                ActiveForm::validate($model),
//                ActiveForm::validateMultiple($modelGiftCardPaymentItems),
//                ActiveForm::validateMultiple($modelPaymentMethodItems)
//            );
//            if(!empty($validationError)){
//                return $validationError;
//            }
//        }

        // Save
        if ($model->load(Yii::$app->request->post())) {
            $this->saveData($model, $modelGiftCardPaymentItems, $modelPaymentMethodItems);
        }
        else{
            $giftCardInfo = GiftCard::getGiftCardInfoByDocId($refDocId);
            if($giftCardInfo) {
                $modelGiftCardPaymentItems[0]->gift_card_id = ArrayHelper::getValue($giftCardInfo, 'id');
            }
        }

        return $this->renderAjax('_form', [
            'model' => $model,
            'hasGiftCardPaymentItem' => $hasGiftCardPaymentItem,
            'modelGiftCardPaymentItems' => $modelGiftCardPaymentItems,
            'hasPaymentMethodItem' => $hasPaymentMethodItem,
            'modelPaymentMethodItems' => $modelPaymentMethodItems,
            'refDocId' => $refDocId,

        ]);
    }

    /**
     * Updates an existing PurchaseOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($docId)
    {
        $model = $this->findModelByDocId($docId);


        $priceAttributes = ['price','cash_back_website_rate'];
        foreach ($priceAttributes as $attribute) {
            $model->$attribute = number_format($model->$attribute, 2);
        }
        // Gift Card Item
        $hasGiftCardPaymentItem = true;
        $modelGiftCardPaymentItems = $model->giftCardPaymentItems;

        $giftCardItemsArray = Yii::$app->request->post('GiftCardPaymentItem');
        if($giftCardItemsArray){
            $modelGiftCardPaymentItems = [];
            foreach ($giftCardItemsArray as $key => $item) {
                $modelGiftCardPaymentItems[$key] = $this->findGiftCardPaymentItemModel($item);

            }
        }

        foreach ($modelGiftCardPaymentItems as $item) {
            $priceAttributes = ['amount'];
            foreach ($priceAttributes as $attribute) {
                $item->$attribute = number_format($item->$attribute, 2);
            }
        }

        // Payment Method
        $hasPaymentMethodItem = true;
        $modelPaymentMethodItems = $model->transactionPaymentMethods;

        $paymentMethodItemsArray = Yii::$app->request->post('Transaction');
        if($paymentMethodItemsArray){
            $modelPaymentMethodItems = [];
            foreach ($paymentMethodItemsArray as $key => $item) {
                $modelPaymentMethodItems[$key] = $this->findTransactionPaymentMethodModel($item);
            }
        }

        foreach ($modelPaymentMethodItems as $item) {
            $priceAttributes = ['spent_amount', 'cash_back_rate'];
            foreach ($priceAttributes as $attribute) {
                $item->$attribute = number_format($item->$attribute, 2);
            }
        }

        // Validation
         $model->load(Yii::$app->request->post());
         Model::loadMultiple($modelGiftCardPaymentItems, Yii::$app->request->post());
         Model::loadMultiple($modelPaymentMethodItems, Yii::$app->request->post());
         if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
             Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
             $validationError = array_merge(
                 ActiveForm::validate($model),
                 ActiveForm::validateMultiple($modelGiftCardPaymentItems),
                 ActiveForm::validateMultiple($modelPaymentMethodItems)
             );
             if(!empty($validationError)){
                 return $validationError;
             }
         }

         // Save
         if ($model->load(Yii::$app->request->post())) {
             $this->saveData($model, $modelGiftCardPaymentItems, $modelPaymentMethodItems);
         }

        return $this->renderAjax('_form', [
             'model' => $model,
             'hasGiftCardPaymentItem' => $hasGiftCardPaymentItem,
             'modelGiftCardPaymentItems' => $modelGiftCardPaymentItems,
             'hasPaymentMethodItem' => $hasPaymentMethodItem,
             'modelPaymentMethodItems' => $modelPaymentMethodItems,
         ]);
    }

    public function actionDelete($docId)
    {
        $model = $this->findModelByDocId($docId);

        try {
            GiftCardPaymentItem::deleteAll(['gift_card_payment_id' => $model->id]);
            if (!$model->delete()) {
                throw new Exception(Yii::t('app', 'The record cannot be deleted.'));
            }
            Transaction::deleteAll(['transaction_doc_id' => $docId]);
            // Delete Image:
            // FileHelper::removeFile($model->image, 'sanction');
            Yii::$app->session->setFlash('success', Yii::t('app', 'The record have been successfully deleted.'));
        }
        catch (Exception $e) {
            $message = $e->getMessage();
            if($e->getName() == CommonHelper::INTEGRITY_CONSTRAINT_VIOLATION){
                $message = "This record has relational data. You must delete the following data first";
                $message .= "<ul>";
                $message .= "<li>Example => Example List</li>";
                $message .= "</ul>";
            }
            Yii::$app->session->setFlash('error', $message);
        }

        return $this->redirect(['/payment-method/payment-method-transaction/gift-card']);
    }

    private function saveData($model, $modelGiftCardPaymentItems, $modelPaymentMethodItems)
    {
        if (isset($model)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $post = Yii::$app->request->post();

                // Gift Card Payment
                $giftCardPaymentInfo = GiftCardPayment::saveGiftCardPayment($model);

                // Gift Card Payment Item
                foreach ($modelGiftCardPaymentItems as $modelGiftCardPaymentItem) {
                    $modelGiftCardPaymentItem->gift_card_payment_id = $giftCardPaymentInfo->id;
                    if (!$modelGiftCardPaymentItem->save()) {
                        $errorMessagePrefix = '<p><b>Gift Card Payment Item</b></p>';
                        throw new Exception(Yii::t('app', $errorMessagePrefix . Html::errorSummary($modelGiftCardPaymentItem)));
                    }
                }

                // Remove Card Payment Item
                $deleteGiftCardPaymentItemIds = isset($post['delete-gift-card-payment-item-ids']) ? $post['delete-gift-card-payment-item-ids'] : '';
                if(!empty($deleteGiftCardPaymentItemIds)) {
                    $giftCardPaymentItemIds = explode(',', $deleteGiftCardPaymentItemIds);
                    foreach ($giftCardPaymentItemIds as $giftCardPaymentItemId) {
                        $modelgiftCardPaymentItem = GiftCardPaymentItem::findOne($giftCardPaymentItemId);
                        if (!$modelgiftCardPaymentItem->delete()) {
                            throw new Exception(Yii::t('app', Html::errorSummary($modelgiftCardPaymentItem)));
                        }
                    }
                }

                /* Start: Payment Methods */
                foreach ($modelPaymentMethodItems as $paymentMethodItem) {

                    switch ($paymentMethodItem->purchasing_account_type) {

                        case 'credit-card':
                            // Payment Account Transactions	"Spent"

                            $modelPaymentMethodItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($giftCardPaymentInfo->doc_id, CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, $paymentMethodItem->purchasing_account_id,$paymentMethodItem->purchasing_account_type);
                            $modelPaymentMethodItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                            $modelPaymentMethodItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                            $modelPaymentMethodItem->transaction_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD_PAYMENT;
                            $modelPaymentMethodItem->transaction_doc_id = $giftCardPaymentInfo->doc_id;
                            $modelPaymentMethodItem->purchasing_account_type = $paymentMethodItem->purchasing_account_type;
                            $modelPaymentMethodItem->purchasing_account_id = $paymentMethodItem->purchasing_account_id;
                            $modelPaymentMethodItem->spent_amount = $paymentMethodItem->spent_amount;
                            $modelPaymentMethodItem->cash_back_rate = $paymentMethodItem->cash_back_rate;
                            $modelPaymentMethodItem->transaction_date = $model->gift_card_pay_date;
                            $modelPaymentMethodItemInfo  = Transaction::saveTransactionInfo($modelPaymentMethodItem);

                            //Credit Card CashBack
                            $modelCreditCardCashBackItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($modelPaymentMethodItem->transaction_doc_id, CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE, $modelPaymentMethodItem->purchasing_account_id,CommonHelper::ACCOUNT_TYPE_CASH_BACK_CREDIT_CARD);
                            $modelCreditCardCashBackItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                            $modelCreditCardCashBackItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE;
                            $modelCreditCardCashBackItem->transaction_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD_PAYMENT;
                            $modelCreditCardCashBackItem->transaction_doc_id = $modelPaymentMethodItem->transaction_doc_id;
                            $modelCreditCardCashBackItem->purchasing_account_id = $modelPaymentMethodItem->purchasing_account_id;
                            if($paymentMethodItem->cash_back_rate){
                                $modelCreditCardCashBackItem->received_amount = CostCalculation::calculateCreditCardCashBack($paymentMethodItem->spent_amount, $paymentMethodItem->cash_back_rate);
                            }

                            $modelCreditCardCashBackItem->transaction_date = $model->gift_card_pay_date;
                            $modelCreditCardCashBackItem->purchasing_account_type = CommonHelper::ACCOUNT_TYPE_CASH_BACK_CREDIT_CARD;
                            $modelCreditCardCashBackItem->purchase_amount = $modelPaymentMethodItem->received_amount;
                            $modelCreditCardCashBackItem->cash_back_rate = $modelPaymentMethodItem->cash_back_rate;
                            $creditCardCashBackItemInfo  = Transaction::saveTransactionInfo($modelCreditCardCashBackItem);
                            break;

                        default:
                            // Payment Account Transactions	"Spent"
                            $modelPaymentMethodItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($giftCardPaymentInfo->doc_id, CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, $paymentMethodItem->purchasing_account_id,$paymentMethodItem->purchasing_account_type);
                            $modelPaymentMethodItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                            $modelPaymentMethodItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                            $modelPaymentMethodItem->transaction_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD_PAYMENT;
                            $modelPaymentMethodItem->transaction_doc_id = $giftCardPaymentInfo->doc_id;
                            $modelPaymentMethodItem->purchasing_account_type = $paymentMethodItem->purchasing_account_type;
                            $modelPaymentMethodItem->purchasing_account_id = $paymentMethodItem->purchasing_account_id;
                            $modelPaymentMethodItem->spent_amount = $paymentMethodItem->spent_amount;
                            $modelPaymentMethodItem->cash_back_rate = $paymentMethodItem->cash_back_rate;
                            $modelPaymentMethodItem->transaction_date = $model->gift_card_pay_date;
                            $modelPaymentMethodItemInfo  = Transaction::saveTransactionInfo($modelPaymentMethodItem);
                            break;
                    }
                }

                // Website CashBack Transaction
                if($giftCardPaymentInfo->cash_back_website_id){
                    $modelWebsiteCashBackItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($giftCardPaymentInfo->doc_id, CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE, $giftCardPaymentInfo->cash_back_website_id,CommonHelper::ACCOUNT_TYPE_CASH_BACK_WEBSITE);

                    $modelWebsiteCashBackItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_GIFT_CARD;
                    $modelWebsiteCashBackItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE;
                    $modelWebsiteCashBackItem->transaction_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD_PAYMENT;
                    $modelWebsiteCashBackItem->transaction_doc_id = $giftCardPaymentInfo->doc_id;
                    $modelWebsiteCashBackItem->purchasing_account_id = $giftCardPaymentInfo->cash_back_website_id;

                    if($giftCardPaymentInfo->cash_back_website_rate){
                        $modelWebsiteCashBackItem->received_amount = CostCalculation::websiteCashBack($giftCardPaymentInfo->price, $giftCardPaymentInfo->cash_back_website_rate);
                    }

                    $modelWebsiteCashBackItem->transaction_date = $giftCardPaymentInfo->gift_card_pay_date;
                    $modelWebsiteCashBackItem->purchasing_account_type = CommonHelper::ACCOUNT_TYPE_CASH_BACK_WEBSITE;
                    $modelWebsiteCashBackItem->purchase_amount = $giftCardPaymentInfo->price;
                    $modelWebsiteCashBackItem->cash_back_rate = $giftCardPaymentInfo->cash_back_website_rate;
                    $websiteCashBackItemInfo  = Transaction::saveTransactionInfo($modelWebsiteCashBackItem);
                }
                /* END: Payment Methods */

                // Remove payment Item
                $deleteOrderPaymentMethodIds = isset($post['delete-payment-method-ids']) ? $post['delete-payment-method-ids'] : '';
                if(!empty($deleteOrderPaymentMethodIds)) {
                    $orderPaymentMethodIds = explode(',', $deleteOrderPaymentMethodIds);
                    foreach ($orderPaymentMethodIds as $orderPaymentMethodId) {
                        $modelPaymentMethod = Transaction::findOne($orderPaymentMethodId);
                        if (!$modelPaymentMethod->delete()) {
                            throw new Exception(Yii::t('app', Html::errorSummary($modelPaymentMethod)));
                        }
                    }
                }

                $transaction->commit();
                echo "1";
                exit();
//                Yii::$app->session->setFlash('success', Yii::t('app', 'Record successfully saved.'));
//                $this->_redirect($model);
            }
            catch (Exception $e) {
                $transaction->rollBack();
               return  Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
    }


    private function _redirect($model){
        $action = Yii::$app->request->post('action', 'save');
        switch ($action) {
            case 'save':
                return $this->redirect(['/gift-card/gift-card']);
                break;
            case 'apply':
                return $this->redirect(['update', 'id' => $model->id]);
                break;
            case 'save2new':
                return $this->redirect(['create']);
                break;
            default:
                return $this->redirect(['index']);
        }
    }

    /**
     * Finds the PurchaseOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PurchaseOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GiftCardPaymentItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    protected function findModelByDocId($docId)
    {
        if (($model = GiftCardPayment::findOne(['doc_id' => $docId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    protected function findModeTransactionlByDocId($docId)
    {
        if (($model = Transaction::findOne(['transaction_doc_id' => $docId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
    protected function findPurchaseOrderModel($id)
    {
        if (($model = PurchaseOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    protected function findInvoiceOrderModel($id)
    {
        if (($model = PurchaseOrderInvoice::findOne($id)) !== null) {
            return $model;
        }
    }

    protected function findTransactionPaymentMethodModel($item)
    {
        if(array_key_exists('id', $item)){
            if (($model = Transaction::findOne($item['id'])) !== null) {
                return $model;
            }
            else {
                return new Transaction();
            }
        }
        else{
            return new Transaction();
        }
    }

    protected function findGiftCardPaymentItemModel($item)
    {
        if(array_key_exists('id', $item)){
            if (($model = GiftCardPaymentItem::findOne($item['id'])) !== null) {
                return $model;
            }
            else {
                return new GiftCardPaymentItem();
            }
        }
        else{
            return new GiftCardPaymentItem();
        }
    }

}
