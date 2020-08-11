<?php

namespace unlock\modules\giftcard\controllers;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\base\Exception;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use unlock\modules\giftcard\models\GiftCard;
use unlock\modules\giftcard\models\GiftCardSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use unlock\modules\core\helpers\Collection;
use unlock\modules\core\helpers\FileHelper;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\core\actions\ExportCsvAction;
use unlock\modules\core\actions\ExportPdfAction;
use unlock\modules\purchaseorder\models\MatchingRule;
use unlock\modules\giftcard\models\GiftCardReload;
use yii\widgets\ActiveForm;
use unlock\modules\core\data\ActiveDataProvider;

/**
 * GiftCardController implements the CRUD actions for GiftCard model.
 */
class GiftCardController extends Controller
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
            'access' => Yii::$app->DynamicAccessRules->customAccess('giftcard/giftcard'),
        ];
    }
    
    /**
     * Lists all GiftCard models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GiftCardSearch();
        $searchModel->org_id = CommonHelper::getLoggedInUserOrgId();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        Collection::setData($dataProvider);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAjaxSearchIndex()
    {
        $searchModel = new GiftCardSearch();
        $searchModel->org_id = CommonHelper::getLoggedInUserOrgId();
        $dataProvider = $searchModel->search(Yii::$app->request->post());

        return $this->renderAjax('_ajaxsearchindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single GiftCard model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new GiftCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($createFrom = null,$dropDownId = null)
    {
        $model = new GiftCard();
        $model->scenario = $model::GIFT_CARD_NEW_INSERT;

        // Match Rule
        $hasMatchingRuleItem = true;
        $modelMatchingRuleItems = [new MatchingRule()];

        $matchingRuleArray = Yii::$app->request->post('MatchingRule');

        if($matchingRuleArray){
            $modelMatchingRuleItems = [];
            foreach ($matchingRuleArray as $key => $item) {
                $modelMatchingRuleItems[$key] = new MatchingRule();
            }
        }

        // Validation
        Model::loadMultiple($modelMatchingRuleItems, Yii::$app->request->post());
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $validateError = array_merge(
                  \yii\widgets\ActiveForm::validate($model)
                 //\yii\widgets\ActiveForm::validateMultiple($modelMatchingRuleItems)
              );
            if(!empty($validateError)){
                return $validateError;
            }
         }



        if ($model->load(Yii::$app->request->post())) {
            $this->saveData($model, $modelMatchingRuleItems,$createFrom,$dropDownId);
        }

        $docId = Yii::$app->request->get('docId');
        $orderId = Yii::$app->request->get('orderId');

        return $this->renderAjax('_form', [
            'model' => $model,
            'hasMatchingRuleItem' => $hasMatchingRuleItem,
            'modelMatchingRuleItems' => $modelMatchingRuleItems,
            'docId' => $docId,
            'orderId' => $orderId,
        ]);
    }
    public function actionChangeStatus($docId){
        $model = $this->findModelByDocId($docId);
        $status = Yii::$app->request->get('status');
        $model->status = $status;
        try {
            if (!$model->save()) {
                throw new Exception(Html::errorSummary($model));
            }
            if($status == CommonHelper::STATUS_ACTIVE){
                Yii::$app->session->setFlash('success', Yii::t('app', $docId.' this gift card has been active.'));
            }elseif ($status == CommonHelper::STATUS_INACTIVE){
                Yii::$app->session->setFlash('success', Yii::t('app', $docId.' this gift card has been inactive.'));
            }
        }
        catch (Exception $e) {
            $message = $e->getMessage();
            Yii::$app->session->setFlash('error', $message);
        }
        return $this->redirect(['index']);
    }
    /**
     * Updates an existing GiftCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($docId)
    {
        $model = $this->findModelByDocId($docId);
        $model->scenario = $model::GIFT_CARD_NEW_UPDATE;
        $modelRelationalData = GiftCard::getGiftCardRelationalData($model['id']);

        $priceAttributes = ['initial_balance', 'price', 'value', 'discount_rate'];
        foreach ($priceAttributes as $attribute) {
            $model->$attribute = Yii::$app->formatter->format($model->$attribute, ['cleanNumber']);
        }

        // Match Rule
        $hasMatchingRuleItem = true;
        $modelMatchingRuleItems = MatchingRule::getAllMatchingRuleItemByDocId($model->doc_id);

        if(empty($modelMatchingRuleItems)){
            $hasMatchingRuleItem = false;
            $modelMatchingRuleItems = [new MatchingRule()];
        }

        $matchingRuleArray = Yii::$app->request->post('MatchingRule');


        if($matchingRuleArray){
            $modelMatchingRuleItems = [];
            foreach ($matchingRuleArray as $key => $item) {
                $modelMatchingRuleItems[$key] = $this->findMatchingRuleModel($item);
            }
        }



        // Validation
        Model::loadMultiple($modelMatchingRuleItems, Yii::$app->request->post());
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
           Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
           $validateError = array_merge(
              \yii\widgets\ActiveForm::validate($model)
              //\yii\widgets\ActiveForm::validateMultiple($modelMatchingRuleItems)
           );
            if(!empty($validateError)){
               return $validateError;
            }
        }

        //

        if ($model->load(Yii::$app->request->post())) {
            $this->saveData($model, $modelMatchingRuleItems);
        }
            return $this->renderAjax('_giftcardview', [
            'model' => $model,
            'hasMatchingRuleItem' => $hasMatchingRuleItem,
            'modelMatchingRuleItems' => $modelMatchingRuleItems,
            'modelRelationalData' => $modelRelationalData,
        ]);
    }

    public function actionUpdateView($docId)
    {
        $model = $this->findModelByDocId($docId);
        $model->scenario = $model::GIFT_CARD_NEW_UPDATE;
        $modelRelationalData = GiftCard::getGiftCardRelationalData($model['id']);

        $priceAttributes = ['initial_balance', 'price', 'value', 'discount_rate'];
        foreach ($priceAttributes as $attribute) {
            $model->$attribute = Yii::$app->formatter->format($model->$attribute, ['cleanNumber']);
        }

        // Match Rule
        $hasMatchingRuleItem = true;
        $modelMatchingRuleItems = MatchingRule::getAllMatchingRuleItemByDocId($model->doc_id);

        if(empty($modelMatchingRuleItems)){
            $hasMatchingRuleItem = false;
            $modelMatchingRuleItems = [new MatchingRule()];
        }

        $matchingRuleArray = Yii::$app->request->post('MatchingRule');


        if($matchingRuleArray){
            $modelMatchingRuleItems = [];
            foreach ($matchingRuleArray as $key => $item) {
                $modelMatchingRuleItems[$key] = $this->findMatchingRuleModel($item);
                $modelMatchingRuleItems[$key]->ref_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD;
                $modelMatchingRuleItems[$key]->ref_doc_id = $model->doc_id;
                $modelMatchingRuleItems[$key]->ref_doc_pk = $model->id;
                $modelMatchingRuleItems[$key]->match_type = CommonHelper::MATCH_TYPE_RULE;
            }
        }


        // Validation
        Model::loadMultiple($modelMatchingRuleItems, Yii::$app->request->post());
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if($matchingRuleArray){
                $validateError = array_merge(
                    \yii\widgets\ActiveForm::validate($model),
                \yii\widgets\ActiveForm::validateMultiple($modelMatchingRuleItems)
                );

                if(!empty($validateError)){
                    return $validateError;
                }
            }else{
                $validationError = \yii\widgets\ActiveForm::validate($model);
                if(!empty($validationError)){
                    return $validationError;
                }
            }

        }

        //

        if ($model->load(Yii::$app->request->post())) {
            $this->saveData($model, $modelMatchingRuleItems);
        }
        return $this->renderAjax('_form', [
            'model' => $model,
            'hasMatchingRuleItem' => $hasMatchingRuleItem,
            'modelMatchingRuleItems' => $modelMatchingRuleItems,
            'modelRelationalData' => $modelRelationalData,
        ]);
    }

    /**
     * Deletes an existing GiftCard model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($docId)
    {
        $model = $this->findModelByDocId($docId);

        try {
            // Delete Matching Rule
            if(!MatchingRule::deleteAll(['ref_doc_id' => $model->doc_id])){
                throw new Exception(Yii::t('app', 'The record cannot be deleted.'));
            }

            if (!$model->delete()) {
                throw new Exception(Yii::t('app', 'The record cannot be deleted.'));
            }

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

        return $this->redirect(['index']);
    }

    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        return $this->redirect(['/gift-card/gift-card-order-summary/cancel', 'id' => $model->order_id]);
    }

    public function actionRefund($id)
    {
        $model = $this->findModel($id);
        return $this->redirect(['/gift-card/gift-card-order-summary/refund', 'id' => $model->order_id]);
    }

    public function actionReload($id)
    {
        $model = $this->findModel($id);
        return $this->redirect(['/gift-card/gift-card-order-summary/reload', 'id' => $model->order_id]);
    }

    public function actionReceipt($id)
    {
        $model = $this->findModel($id);
        return $this->redirect(['/gift-card/gift-card-order-summary/receipt', 'id' => $model->order_id]);

    }

    public function actionShipment($id)
    {
        $model = $this->findModel($id);
        return $this->redirect(['/gift-card/gift-card-order-summary/shipment', 'id' => $model->order_id]);

    }

    public function actionAddExpense($id)
    {
        $model = $this->findModel($id);
        return $this->redirect(['/gift-card/gift-card-order-summary/add-expense', 'id' => $model->order_id]);
    }

    /**
    * Save Data.
    * @param object $model
    * @return mixed
    */
    private function saveData($model, $modelMatchingRuleItems,$createFrom = null,$dropDownId = null){

        if (isset($model)) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $post = Yii::$app->request->post();

                if (!$model->save()) {
                    throw new Exception(Yii::t('app', Html::errorSummary($model)));
                }

                // Save Doc Item Matching Rule
                $modelMatchingRuleItem = $this->findMatchingRuleDocId($model->doc_id);
                $modelMatchingRuleItem->ref_doc_type = CommonHelper::DOC_TYPE_GIFT_CARD;
                $modelMatchingRuleItem->ref_doc_id = $model->doc_id;
                $modelMatchingRuleItem->ref_doc_pk = $model->id;
                $modelMatchingRuleItem->match_type = CommonHelper::MATCH_TYPE_ITEM_NAME;
                $modelMatchingRuleItem->match_text = $model->gift_card_number;
                if (!$modelMatchingRuleItem->save()) {
                    throw new Exception(Yii::t('app', Html::errorSummary($modelMatchingRuleItem)));
                }

                // Save Matching Rules
                if(Yii::$app->request->post('MatchingRule')){
                    foreach ($modelMatchingRuleItems as $modelMatchingRuleItem) {
                        if (!$modelMatchingRuleItem->save()) {
                            throw new Exception(Yii::t('app', Html::errorSummary($modelMatchingRuleItem)));
                        }
                    }
                }


                $transaction->commit();
//                Yii::$app->session->setFlash('success', Yii::t('app', 'Record successfully saved.'));
                if($createFrom == 'purchaseOrderPayment'){
                    $return_data = array(
                        'createFrom'         =>'purchaseOrderPayment',
                        'dropDownId'         => $dropDownId,
                        'giftCardNumber'     => "******".$model->gift_card_number,
                         'giftCardDiscount'  => $model->discount_rate,
                        'giftCardId'         => $model->id


                    );

                    print_r(json_encode($return_data));

                    exit();
                }

                $return_data = array(
                    'created'            =>'giftcardEditDoc',
                    'id'            =>$model->id,
                    'doc_id'       => $model->doc_id,
                );
                print_r(json_encode($return_data));
                exit();

                // $this->_redirect($model);
            }
            catch (Exception $e) {
                $transaction->rollBack();
                echo $e->getMessage();
                exit();
                // Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
    }
    
    private function _redirect($model){
        $action = Yii::$app->request->post('action', 'save');
        switch ($action) {
            case 'save':
                return $this->redirect(['index']);
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
     * Finds the GiftCard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return GiftCard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GiftCard::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    protected function findModelByDocId($docId)
    {
        if (($model = GiftCard::findOne(['doc_id' => $docId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    protected function findMatchingRuleModel($item)
    {
        if(array_key_exists('id', $item)){
            if (($model = MatchingRule::findOne($item['id'])) !== null) {
                return $model;
            }
            else {
                return new MatchingRule();
            }
        }
        else{
            return new MatchingRule();
        }
    }

    protected function findMatchingRuleDocId($docId)
    {
        if (($model = MatchingRule::findOne(['ref_doc_id' => $docId, 'match_type' => CommonHelper::MATCH_TYPE_ITEM_NAME])) !== null) {
            return $model;
        }
        else {
            return new MatchingRule();
        }
    }
}
