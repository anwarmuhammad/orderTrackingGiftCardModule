<?php

namespace unlock\modules\giftcard\controllers;

use unlock\modules\giftcard\models\GiftCard;
use unlock\modules\purchaseorder\models\Transaction;
use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use unlock\modules\purchaseorder\models\PurchaseOrderPayment;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use unlock\modules\core\helpers\Collection;
use unlock\modules\core\helpers\FileHelper;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\core\actions\ExportCsvAction;
use unlock\modules\core\actions\ExportPdfAction;
use unlock\modules\purchaseorder\models\PurchaseOrder;
use unlock\modules\purchaseorder\models\PurchaseOrderItemSearch;
use unlock\modules\paymentmethod\models\TransactionPaymentMethod;
use unlock\modules\paymentmethod\models\PaymentMethodTransactionSearch;
use unlock\modules\purchaseorder\models\PurchaseOrderDocItem;
use unlock\modules\cashback\models\TransactionCashBack;
use unlock\modules\purchasingaccount\models\PurchasingAccount;
use unlock\modules\product\models\Product;
use unlock\modules\purchasingaccount\models\PurchasingAccountCharge;
use unlock\modules\giftcard\models\GiftCardReload;
use unlock\modules\cashback\models\CashBackWebsite;
use unlock\modules\core\helpers\CostCalculation;


/**
 * PurchaseOrderPaymentController implements the CRUD actions for PurchaseOrderInvoice model.
 */
class GiftCardReloadController extends Controller
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
        $model = new GiftCardReload();
        $model->gift_card_reload_date = date("Y-m-d");

        $giftCardBalance = null;

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
        Model::loadMultiple($modelPaymentMethodItems, Yii::$app->request->post());
         if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
             Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
             $validationError = array_merge(
                 ActiveForm::validate($model),
                 ActiveForm::validateMultiple($modelPaymentMethodItems)
             );
             if(!empty($validationError)){
                 return $validationError;
             }
         }

        // Save
        if ($model->load(Yii::$app->request->post())) {
            $this->saveData($model, $modelPaymentMethodItems);
        }
        else {
            $giftCardInfo = GiftCard::getGiftCardInfoByDocId($refDocId);
            if($giftCardInfo) {
                $model->gift_card_id = ArrayHelper::getValue($giftCardInfo, 'id');
                $giftCardBalance = Yii::$app->formatter->format(Transaction::balanceCalculation(CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, CommonHelper::ACCOUNT_TYPE_GIFT_CARD, $model->gift_card_id),'money');
            }
        }

        return $this->renderAjax('_form', [
            'model' => $model,
            'giftCardBalance' => $giftCardBalance,
            'hasPaymentMethodItem' => $hasPaymentMethodItem,
            'modelPaymentMethodItems' => $modelPaymentMethodItems,

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

        $priceAttributes = ['value', 'price','discount_rate','cash_back_website_rate'];
        foreach ($priceAttributes as $attribute) {
            $model->$attribute = number_format($model->$attribute, 2);
        }

        $giftCardBalance = Yii::$app->formatter->format(Transaction::balanceCalculation(CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, CommonHelper::ACCOUNT_TYPE_GIFT_CARD, $model->gift_card_id),'money');

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
        Model::loadMultiple($modelPaymentMethodItems, Yii::$app->request->post());
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $validationError = array_merge(
                ActiveForm::validate($model),
                ActiveForm::validateMultiple($modelPaymentMethodItems)
            );
            if(!empty($validationError)){
                return $validationError;
            }
        }

        // Save
        if ($model->load(Yii::$app->request->post())) {
            $this->saveData($model, $modelPaymentMethodItems);
        }

        return $this->renderAjax('_form2', [
            'model' => $model,
            'giftCardBalance' => $giftCardBalance,
            'hasPaymentMethodItem' => $hasPaymentMethodItem,
            'modelPaymentMethodItems' => $modelPaymentMethodItems,
        ]);
    }

    public function actionDelete($docId)
    {
        $model = $this->findModelByDocId($docId);
        try {
            if (!$model->delete()) {
                throw new Exception(Yii::t('app', 'The record cannot be deleted.'));
            }
            Transaction::deleteAll(['transaction_doc_id' => $docId]);
//            Transaction::deleteAll(['transaction_doc_id' => $docId]);

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

    private function saveData($model, $modelPaymentMethodItems)
    {
        if (isset($model)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $post = Yii::$app->request->post();
                $giftCardReloadInfo = GiftCardReload::saveGiftCardReload($model);

                // Payment Account Transactions	"Reload Amount"
                $modelPaymentMethodItemCredit = Transaction::findModelTransactionPaymentMethodItemByAccountType($model->doc_id, CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, $model->gift_card_id,CommonHelper::ACCOUNT_TYPE_GIFT_CARD);
                $modelPaymentMethodItemCredit->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_GIFT_CARD;
                $modelPaymentMethodItemCredit->transaction_type =  CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                $modelPaymentMethodItemCredit->transaction_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD_RELOAD;
                $modelPaymentMethodItemCredit->transaction_doc_id = $model->doc_id;
                $modelPaymentMethodItemCredit->transaction_date = $giftCardReloadInfo->gift_card_reload_date;
                $modelPaymentMethodItemCredit->purchasing_account_type = CommonHelper::ACCOUNT_TYPE_GIFT_CARD;
                $modelPaymentMethodItemCredit->purchasing_account_id = $giftCardReloadInfo->gift_card_id;
                $modelPaymentMethodItemCredit->cash_back_rate = $giftCardReloadInfo->cash_back_website_rate;
                $modelPaymentMethodItemCredit->received_amount = $giftCardReloadInfo->value;
                $modelPaymentMethodItemCredit->discount_amount = $giftCardReloadInfo->value - $giftCardReloadInfo->price;
                $modelPaymentMethodItemCredit->note = $giftCardReloadInfo->note;
                $transactionPaymentMethodCreditInfo      = Transaction::saveTransactionInfo($modelPaymentMethodItemCredit);

                // Payment Account Transactions	"CashBack Website Amount"
                if($model->cash_back_website_id){
                    $modelPaymentMethodItemWebsiteCashBack = Transaction::findModelTransactionPaymentMethodItemByAccountType($model->doc_id, CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE, $model->cash_back_website_id,CommonHelper::ACCOUNT_TYPE_CASH_BACK_WEBSITE);
                    $modelPaymentMethodItemWebsiteCashBack->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_GIFT_CARD;
                    $modelPaymentMethodItemWebsiteCashBack->transaction_type =  CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE;
                    $modelPaymentMethodItemWebsiteCashBack->transaction_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD_RELOAD;
                    $modelPaymentMethodItemWebsiteCashBack->transaction_doc_id = $model->doc_id;
                    $modelPaymentMethodItemWebsiteCashBack->transaction_date = $giftCardReloadInfo->gift_card_reload_date;
                    $modelPaymentMethodItemWebsiteCashBack->purchasing_account_type = CommonHelper::ACCOUNT_TYPE_CASH_BACK_WEBSITE;
                    $modelPaymentMethodItemWebsiteCashBack->purchasing_account_id = $model->cash_back_website_id;
                    $modelPaymentMethodItemWebsiteCashBack->cash_back_rate = $giftCardReloadInfo->cash_back_website_rate;
                    $modelPaymentMethodItemWebsiteCashBack->received_amount = CostCalculation::websiteCashBack($giftCardReloadInfo->price, $model->cash_back_website_rate);
                    $modelPaymentMethodItemWebsiteCashBack->note = $giftCardReloadInfo->note;
                    $modelPaymentMethodItemWebsiteCashBackInfo      = Transaction::saveTransactionInfo($modelPaymentMethodItemWebsiteCashBack);
                }


                /* Start: Payment Methods */
                foreach ($modelPaymentMethodItems as $paymentMethodItem) {

                    switch ($paymentMethodItem->purchasing_account_type) {

                        case 'credit-card':

                            // Payment Account Transactions	"Spent"
                            $modelPaymentMethodItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($giftCardReloadInfo->doc_id, CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, $paymentMethodItem->purchasing_account_id,$paymentMethodItem->purchasing_account_type);
                            $modelPaymentMethodItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                            $modelPaymentMethodItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                            $modelPaymentMethodItem->transaction_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD_RELOAD;
                            $modelPaymentMethodItem->transaction_doc_id = $giftCardReloadInfo->doc_id;
                            $modelPaymentMethodItem->purchasing_account_type = $paymentMethodItem->purchasing_account_type;
                            $modelPaymentMethodItem->purchasing_account_id = $paymentMethodItem->purchasing_account_id;
                            $modelPaymentMethodItem->spent_amount = $paymentMethodItem->spent_amount;
                            $modelPaymentMethodItem->cash_back_rate = $paymentMethodItem->cash_back_rate;
                            if (!$modelPaymentMethodItem->save()) {
                                $errorMessagePrefix = '<p><b>Transaction</b></p>';
                                throw new Exception(Yii::t('app', $errorMessagePrefix . Html::errorSummary($modelPaymentMethodItem)));
                            }

                            // Payment Account Transactions	"Received"
                            $modelPaymentMethodItemCashBack = Transaction::findModelTransactionPaymentMethodItemByAccountType($giftCardReloadInfo->doc_id, CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE, $paymentMethodItem->purchasing_account_id,CommonHelper::ACCOUNT_TYPE_CASH_BACK_CREDIT_CARD);
                            $modelPaymentMethodItemCashBack->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_CASH_BACK_SOURCE;
                            $modelPaymentMethodItemCashBack->transaction_type =  CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE;
                            $modelPaymentMethodItemCashBack->transaction_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD_RELOAD;
                            $modelPaymentMethodItemCashBack->transaction_doc_id = $giftCardReloadInfo->doc_id;
                            $modelPaymentMethodItemCashBack->purchasing_account_type = CommonHelper::ACCOUNT_TYPE_CASH_BACK_CREDIT_CARD;
                            $modelPaymentMethodItemCashBack->purchasing_account_id = $paymentMethodItem->purchasing_account_id;
                            $modelPaymentMethodItemCashBack->received_amount = CostCalculation::calculateCreditCardCashBack($paymentMethodItem->spent_amount, $paymentMethodItem->cash_back_rate);
                            $modelPaymentMethodItemCashBack->cash_back_rate = $paymentMethodItem->cash_back_rate;
                            if (!$modelPaymentMethodItemCashBack->save()) {
                                $errorMessagePrefix = '<p><b>Transaction</b></p>';
                                throw new Exception(Yii::t('app', $errorMessagePrefix . Html::errorSummary($modelPaymentMethodItem)));
                            }
                            break;

                        default:
                            // Payment Account Transactions	"Spent"
                            $modelPaymentMethodItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($giftCardReloadInfo->doc_id, CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, $paymentMethodItem->purchasing_account_id,$paymentMethodItem->purchasing_account_type);
                            $modelPaymentMethodItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                            $modelPaymentMethodItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                            $modelPaymentMethodItem->transaction_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD_RELOAD;
                            $modelPaymentMethodItem->transaction_doc_id = $giftCardReloadInfo->doc_id;
                            $modelPaymentMethodItem->purchasing_account_type = $paymentMethodItem->purchasing_account_type;
                            $modelPaymentMethodItem->purchasing_account_id = $paymentMethodItem->purchasing_account_id;
                            $modelPaymentMethodItem->spent_amount = $paymentMethodItem->spent_amount;
                            $modelPaymentMethodItem->cash_back_rate = $paymentMethodItem->cash_back_rate;
                            if (!$modelPaymentMethodItem->save()) {
                                $errorMessagePrefix = '<p><b>Transaction</b></p>';
                                throw new Exception(Yii::t('app', $errorMessagePrefix . Html::errorSummary($modelPaymentMethodItem)));
                            }

                            break;
                    }
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
                return Yii::$app->session->setFlash('error', $e->getMessage());
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
        if (($model = PurchasingAccountCharge::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    protected function findModelByDocId($docId)
    {
        if (($model = GiftCardReload::findOne(['doc_id' => $docId])) !== null) {
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
}
