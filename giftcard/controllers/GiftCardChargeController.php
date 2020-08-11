<?php

namespace unlock\modules\giftcard\controllers;

use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use unlock\modules\category\models\Category;
use unlock\modules\giftcard\models\GiftCard;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\core\helpers\CostCalculation;
use unlock\modules\purchaseorder\models\Transaction;
use unlock\modules\purchaseorder\models\PurchaseOrder;
use unlock\modules\cashback\models\TransactionCashBack;
use unlock\modules\paymentmethod\models\TransactionPaymentMethod;
use unlock\modules\purchasingaccount\models\PurchasingAccountCharge;


/**
 * GiftCardChargeController implements the CRUD actions for PurchasingAccountCharge model.
 */
class GiftCardChargeController extends Controller
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
            'access' => Yii::$app->DynamicAccessRules->customAccess('giftcard/giftcardcharge'),
        ];
    }

    /**
     * Creates a new PurchaseOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($refDocId=null)
    {

        $model = new PurchasingAccountCharge();
        $model->purchasing_account_charge_date = date("Y-m-d");

        // Validation
        $model->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $validationError = array_merge(
                ActiveForm::validate($model)
            );
            if(!empty($validationError)){
                return $validationError;
            }
        }

        // Save
        if ($model->load(Yii::$app->request->post())) {
            $this->saveData($model);
        }
        else{
            $giftCardInfo = GiftCard::getGiftCardInfoByDocId($refDocId);
            if($giftCardInfo) {
                $model->purchasing_account_type = CommonHelper::ACCOUNT_TYPE_GIFT_CARD;
                $model->purchasing_account_id = ArrayHelper::getValue($giftCardInfo, 'id');
                $model->supplier_id = ArrayHelper::getValue($giftCardInfo, 'supplier_id');
                $balance = Yii::$app->formatter->format(Transaction::balanceCalculation(CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, $model->purchasing_account_type, $model->purchasing_account_id),'money');
                $creditLimit =Yii::$app->formatter->format(0,'money');
            }
        }

        $category = new Category();

        return $this->renderAjax('_form', [
            'model' => $model,
            'balance' => $balance,
            'creditLimit' => $creditLimit,
            'category' => $category,
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
        $priceAttributes = ['amount','cash_back_website_rate'];
        foreach ($priceAttributes as $attribute) {
            $model->$attribute = number_format($model->$attribute, 2);
        }
        $balance = Yii::$app->formatter->format(Transaction::balanceCalculation(CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, $model->purchasing_account_type, $model->purchasing_account_id),'money');
        $creditLimit =Yii::$app->formatter->format(0,'money');
        // Validation
        $model->load(Yii::$app->request->post());
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $validationError = array_merge(
                ActiveForm::validate($model)
            );
            if(!empty($validationError)){
                return $validationError;
            }
        }

        // Save
        if ($model->load(Yii::$app->request->post())) {
            $this->saveData($model);
        }
        $category = new Category();
        return $this->renderAjax('_form', [
            'model' => $model,
            'balance' => $balance,
            'creditLimit' => $creditLimit,
            'category' => $category,
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

        return $this->redirect(['/purchase-order/purchase-order-summary/index']);
    }

    private function saveData($model)
    {
        if (isset($model)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $post = Yii::$app->request->post();

                $purchasingAccountChargeInfo = PurchasingAccountCharge::savePurchasingAccountCharge($model);
                $orderId = CommonHelper::getPurchaseOrderIdByOrderNumber($purchasingAccountChargeInfo->order_number);

                // Payment Account Transactions	"Spent"
                $modelPaymentMethodItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($purchasingAccountChargeInfo->doc_id, CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, $purchasingAccountChargeInfo->purchasing_account_id,$purchasingAccountChargeInfo->purchasing_account_type);
                $modelPaymentMethodItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                $modelPaymentMethodItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT;
                $modelPaymentMethodItem->transaction_doc_type = CommonHelper::DOC_TYPE_PURCHASING_ACCOUNT_CHARGE;
                $modelPaymentMethodItem->transaction_doc_id = $purchasingAccountChargeInfo->doc_id;
                $modelPaymentMethodItem->transaction_date = $purchasingAccountChargeInfo->purchasing_account_charge_date;
                $modelPaymentMethodItem->purchasing_account_type = $purchasingAccountChargeInfo->purchasing_account_type;
                $modelPaymentMethodItem->purchasing_account_id = $purchasingAccountChargeInfo->purchasing_account_id;
                $modelPaymentMethodItem->cash_back_rate = $purchasingAccountChargeInfo->cash_back_website_rate;
                $modelPaymentMethodItem->spent_amount = $purchasingAccountChargeInfo->amount;
                if($purchasingAccountChargeInfo->purchasing_account_type == CommonHelper::ACCOUNT_TYPE_GIFT_CARD){
                    $giftCardCumulativeDiscount = Transaction::cumulativeDiscountCalculationByGiftCardId(CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, CommonHelper::ACCOUNT_TYPE_GIFT_CARD, $purchasingAccountChargeInfo->purchasing_account_id);
                    $modelPaymentMethodItem->discount_amount = -($purchasingAccountChargeInfo->amount*$giftCardCumulativeDiscount);
                }

                $modelPaymentMethodItem->category_id = $purchasingAccountChargeInfo->category_id;
                $modelPaymentMethodItem->note = $purchasingAccountChargeInfo->note;
                if($orderId){
                    $modelPaymentMethodItem->order_id = $orderId;
                }

                $transactionPaymentMethodInfo      = Transaction::saveTransactionInfo($modelPaymentMethodItem);

                switch ($purchasingAccountChargeInfo->purchasing_account_type) {

                    case 'credit-card':

                        //Transaction CashBack Method Credit Card CashBack Item
                        $modelCashBackPaymentMethodItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($purchasingAccountChargeInfo->doc_id, CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE, $purchasingAccountChargeInfo->purchasing_account_id, CommonHelper::ACCOUNT_TYPE_CASH_BACK_CREDIT_CARD);
                        $modelCashBackPaymentMethodItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_CASH_BACK_SOURCE;
                        $modelCashBackPaymentMethodItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE;
                        $modelCashBackPaymentMethodItem->transaction_doc_type = CommonHelper::DOC_TYPE_PURCHASING_ACCOUNT_CHARGE;
                        $modelCashBackPaymentMethodItem->transaction_doc_id = $purchasingAccountChargeInfo->doc_id;
                        $modelCashBackPaymentMethodItem->cash_back_rate = $purchasingAccountChargeInfo->cash_back_credit_card_rate;
                        $modelCashBackPaymentMethodItem->received_amount = CostCalculation::calculateCreditCardCashBack($purchasingAccountChargeInfo->amount, $purchasingAccountChargeInfo->cash_back_credit_card_rate);
                        $modelCashBackPaymentMethodItem->transaction_date = $purchasingAccountChargeInfo->purchasing_account_charge_date;
                        $modelCashBackPaymentMethodItem->purchasing_account_id = $purchasingAccountChargeInfo->purchasing_account_id;
                        $modelCashBackPaymentMethodItem->purchasing_account_type = CommonHelper::ACCOUNT_TYPE_CASH_BACK_CREDIT_CARD;
                        if($orderId){
                            $modelCashBackPaymentMethodItem->order_id = $orderId;
                        }

                        $transactionCashBackDebitInfo      = Transaction::saveTransactionInfo($modelCashBackPaymentMethodItem);

                        // Website CashBack Transaction
                        if($purchasingAccountChargeInfo->cash_back_website_id){
                            $modelWebsiteCashBackItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($purchasingAccountChargeInfo->doc_id, CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE, $purchasingAccountChargeInfo->cash_back_website_id,CommonHelper::ACCOUNT_TYPE_CASH_BACK_WEBSITE);
                            $modelWebsiteCashBackItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_CASH_BACK_SOURCE;
                            $modelWebsiteCashBackItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE;
                            $modelWebsiteCashBackItem->transaction_doc_type = CommonHelper::DOC_TYPE_PURCHASING_ACCOUNT_CHARGE;
                            $modelWebsiteCashBackItem->transaction_doc_id = $purchasingAccountChargeInfo->doc_id;
                            $modelWebsiteCashBackItem->purchasing_account_id = $purchasingAccountChargeInfo->cash_back_website_id;
                            $modelWebsiteCashBackItem->purchasing_account_type = CommonHelper::ACCOUNT_TYPE_CASH_BACK_WEBSITE;
                            $modelWebsiteCashBackItem->received_amount = CostCalculation::websiteCashBack($purchasingAccountChargeInfo->amount, $purchasingAccountChargeInfo->cash_back_website_rate);
                            $modelWebsiteCashBackItem->transaction_date = $purchasingAccountChargeInfo->purchasing_account_charge_date;
                            $modelWebsiteCashBackItem->purchase_amount = $purchasingAccountChargeInfo->amount;
                            $modelWebsiteCashBackItem->cash_back_rate = $purchasingAccountChargeInfo->cash_back_website_rate;
                            if($orderId){
                                $modelWebsiteCashBackItem->order_id = $orderId;
                            }

                            $websiteCashBackItemInfo  = Transaction::saveTransactionInfo($modelWebsiteCashBackItem);
                        }

                        break;

                    default:

                        // Website CashBack Transaction
                        if($purchasingAccountChargeInfo->cash_back_website_id){
                            $modelWebsiteCashBackItem = Transaction::findModelTransactionPaymentMethodItemByAccountType($purchasingAccountChargeInfo->doc_id, CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE, $purchasingAccountChargeInfo->cash_back_website_id,CommonHelper::ACCOUNT_TYPE_CASH_BACK_WEBSITE);
                            $modelWebsiteCashBackItem->scenario = Transaction::SCENARIO_TRANSACTION_TYPE_CASH_BACK_SOURCE;
                            $modelWebsiteCashBackItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_CASH_BACK_SOURCE;
                            $modelWebsiteCashBackItem->transaction_doc_type = CommonHelper::DOC_TYPE_PURCHASING_ACCOUNT_CHARGE;
                            $modelWebsiteCashBackItem->transaction_doc_id = $purchasingAccountChargeInfo->doc_id;
                            $modelWebsiteCashBackItem->purchasing_account_id = $purchasingAccountChargeInfo->cash_back_website_id;
                            $modelWebsiteCashBackItem->purchasing_account_type = CommonHelper::ACCOUNT_TYPE_CASH_BACK_WEBSITE;
                            $modelWebsiteCashBackItem->received_amount = CostCalculation::websiteCashBack($purchasingAccountChargeInfo->amount, $purchasingAccountChargeInfo->cash_back_website_rate);
                            $modelWebsiteCashBackItem->transaction_date = $purchasingAccountChargeInfo->purchasing_account_charge_date;
                            $modelWebsiteCashBackItem->purchase_amount = $purchasingAccountChargeInfo->amount;
                            $modelWebsiteCashBackItem->cash_back_rate = $purchasingAccountChargeInfo->cash_back_website_rate;
                            if($orderId){
                                $modelWebsiteCashBackItem->order_id = $orderId;
                            }

                            $websiteCashBackItemInfo  = Transaction::saveTransactionInfo($modelWebsiteCashBackItem);
                        }

                        break;
                }

                $transaction->commit();

                if (isset($_REQUEST['refDocId'])){
                    if (!empty($_REQUEST['refDocId'])) {
                        $return_data = array(
                            'doc' => 'giftCard',
                            'doc_id' => $_REQUEST['refDocId'],
                        );
                        print_r(json_encode($return_data));
                        exit();
                    }
                }

                echo "1";
                exit();
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
                return $this->redirect(['/gift-card/gift-card/index']);
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
        if (($model = PurchasingAccountCharge::findOne(['doc_id' => $docId])) !== null) {
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
}
