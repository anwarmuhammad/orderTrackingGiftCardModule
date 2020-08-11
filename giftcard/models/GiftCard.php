<?php

namespace unlock\modules\giftcard\models;

use unlock\modules\inbox\inboxOrder\models\InboxOrder;
use unlock\modules\inbox\inboxOrder\models\InboxTransaction;
use unlock\modules\purchaseorder\models\MatchingRule;
use unlock\modules\purchaseorder\models\Transaction;
use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\usermanager\user\models\User;
use unlock\modules\organization\models\Organization;

/**
 * This is the model class for table "{{%gift_card}}".
 *
 * @property string $id
 * @property string $doc_id
 * @property integer $org_id
 * @property string $supplier_id
 * @property string $gift_card_seller_id
 * @property string $gift_card_seller_account_id
 * @property string $gift_card_title
 * @property string $gift_card_number
 * @property string $gift_card_pin
 * @property integer $ordered_quantity
 * @property integer $shipped_quantity
 * @property integer $cancelled_quantity
 * @property integer $received_quantity
 * @property integer $return_quantity
 * @property integer $refund_quantity
 * @property string $discount_rate
 * @property string $value
 * @property string $price
 * @property string $initial_balance
 * @property string $initial_balance_date
 * @property string $purchased_date
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $status
 *
 * @property Organization $org
 * @property User $createdBy
 * @property User $updatedBy
 * @property GiftCardReload[] $giftCardReloads
 */

class GiftCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    
    const GIFT_CARD_NEW_INSERT = 'new_inselt';
    const GIFT_CARD_NEW_UPDATE = 'new_update';


    public static function tableName()
    {
        return '{{%gift_card}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gift_card_number','supplier_id'], 'required','on'=> self::GIFT_CARD_NEW_INSERT],
            [['value', 'price'], 'safe'],
            [['gift_card_number', 'value', 'price','supplier_id'], 'required','on'=> self::GIFT_CARD_NEW_UPDATE],
            [['org_id', 'supplier_id', 'gift_card_seller_account_id', 'gift_card_seller_id', 'ordered_quantity', 'shipped_quantity', 'cancelled_quantity', 'received_quantity', 'return_quantity', 'refund_quantity', 'created_by', 'updated_by', 'status'], 'integer'],
            [['purchased_date', 'initial_balance_date', 'created_at', 'updated_at'], 'safe'],
            [['value', 'price', 'initial_balance'], 'number'],
            ['value', 'compare', 'compareAttribute' => 'price', 'operator' => '>=', 'type' => 'number'],
            [['discount_rate'], 'safe'],
            [['doc_id', 'gift_card_pin'], 'string', 'max' => 100],
            [['gift_card_number'], 'match', 'pattern' => '/^[0-9]*$/', 'message' => 'Please enter last 4 digits only'],
            [['gift_card_number'], 'string', 'min' => 4,'tooShort'=>'Please enter last 4 digits',],
            [['gift_card_number'], 'string', 'max' => 4, 'tooLong' => 'Please enter last 4 digits only',],
            [['gift_card_title'], 'string', 'max' => 255],
            [['doc_id'], 'unique'],
//            [['gift_card_number'], 'unique', 'on'=> self::GIFT_CARD_NEW_INSERT],
            [['org_id'], 'exist', 'skipOnError' => true, 'targetClass' => Organization::className(), 'targetAttribute' => ['org_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            // [['gift_card_number'], 'unique', 'targetAttribute' => ['gift_card_seller_id','supplier_id', 'gift_card_number', 'org_id'], 'message' => 'Account "{value}" has already  been taken.', 'on'=> self::GIFT_CARD_NEW_INSERT],
            [['gift_card_number'], 'unique', 'targetAttribute' => ['gift_card_number', 'supplier_id','org_id'], 'message' => 'Account "{value}" has already  been taken.', 'on'=> self::GIFT_CARD_NEW_INSERT],
            [['gift_card_number'], 'unique', 'targetAttribute' => ['gift_card_number', 'supplier_id','org_id'], 'message' => 'Account "{value}" has already  been taken.', 'on'=> self::GIFT_CARD_NEW_UPDATE],


        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'doc_id' => Yii::t('app', 'Doc ID'),
            'org_id' => Yii::t('app', 'Org ID'),
            'supplier_id' => Yii::t('app', 'Supplier'),
            'gift_card_seller_account_id' => Yii::t('app', 'Gift Card Seller Account'),
            'gift_card_seller_id' => Yii::t('app', 'Gift Card Seller'),
            'gift_card_title' => Yii::t('app', 'Card Title'),
            'gift_card_number' => Yii::t('app', 'Account'),
            'gift_card_pin' => Yii::t('app', 'PIN'),
            'ordered_quantity' => Yii::t('app', 'Ordered Quantity'),
            'shipped_quantity' => Yii::t('app', 'Shipped Quantity'),
            'cancelled_quantity' => Yii::t('app', 'Cancelled Quantity'),
            'received_quantity' => Yii::t('app', 'Received Quantity'),
            'return_quantity' => Yii::t('app', 'Return Quantity'),
            'refund_quantity' => Yii::t('app', 'Refund Quantity'),
            'discount_rate' => Yii::t('app', 'Discount %'),
            'value' => Yii::t('app', 'Value'),
            'price' => Yii::t('app', 'Price'),
            'initial_balance' => Yii::t('app', 'Initial Balance'),
            'initial_balance_date' => Yii::t('app', 'Initial Balance Date'),
            'purchased_date' => Yii::t('app', 'Purchased Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrg()
    {
        return $this->hasOne(Organization::className(), ['id' => 'org_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGiftCardReloads()
    {
        return $this->hasMany(GiftCardReload::className(), ['gift_card_id' => 'id']);
    }

    /** Get All Active & Inactive GiftCard Drop Down List*/
    public static function allGiftCardDropDownList($status = [])
    {
        $checkStatus=!empty($status)?$status:CommonHelper::STATUS_ACTIVE;
        $model = self::find()
            ->where(['status' => $checkStatus])
            ->andWhere(['org_id' => CommonHelper::getLoggedInUserOrgId()])
            ->orderBy('gift_card_number ASC')
            ->all();

        return  ArrayHelper::map($model, 'gift_card_number', 'gift_card_number');
    }


    /** Get All Active GiftCard Drop Down List*/
    public static function giftCardDropDownList($status = CommonHelper::STATUS_ACTIVE)
    {
        $model = self::find()
            ->where('status =:status', [':status' => $status])
            ->all();

        return  ArrayHelper::map($model, 'id', 'gift_card_number');
    }

    public static function giftCardDropDownList2($giftCardId)
    {

        $modelInactiveItem = self::find()
            ->where(['id' => $giftCardId])
            ->andWhere(['org_id' => CommonHelper::getLoggedInUserOrgId()])
            ->orderBy('gift_card_number ASC');


        $modelActiveItem = self::find()
            ->where(['status' => CommonHelper::STATUS_ACTIVE])
            ->andWhere(['org_id' => CommonHelper::getLoggedInUserOrgId()])
            ->orderBy('gift_card_number ASC');


        $model = (new yii\db\Query())
            ->select('*')
            ->from($modelInactiveItem->union($modelActiveItem))
            ->orderBy('gift_card_number ASC')
            ->all();


        return  ArrayHelper::map($model, 'id', 'gift_card_number');
    }

    public static function giftCardOptionsDropDownList()
    {
        $model = GiftCard::find()
            ->where('status =:status', [':status' => CommonHelper::STATUS_ACTIVE])
            ->all();

        $data = [];
        foreach ($model as $item) {
            $data[$item->id] = [
                'data-price' => Yii::$app->formatter->format( $item->price, ['cleanNumber'])
            ];
        }

        return $data;

    }

    public static function giftCardsWithPriceDropDownList()
    {
        $model = GiftCard::find()
            ->where('status =:status', [':status' => CommonHelper::STATUS_ACTIVE])
            ->all();

        $data = [];
        foreach ($model as $item) {
            $data['items'][$item->id] = $item->gift_card_number;
            $data['options'][$item->id] = [
                'data-price' =>  $item->price
            ];
        }

        return $data;

    }

    public static function getGiftCardList($status = CommonHelper::STATUS_ACTIVE)
    {
        return self::find()
            ->where(['status' => $status])
            ->all();
    }

    public static function getGiftCardCurrentBalanceByAccountId($id)
    {

        $model = GiftCard::find()
            ->select('initial_balance')
            ->where(['id' => $id])
            ->one();

        return isset($model->initial_balance) ? $model->initial_balance : 0;
    }

    public static function getGiftCardInfoById($id)
    {
        return GiftCard::find()
            ->where(['id' => $id])
            ->one();
    }

    public static function getGiftCardInfoByDocId($docId)
    {
        return self::find()
            ->where(['doc_id' => $docId])
            ->one();
    }

    public static function getGiftCardsInfoByNumber($giftCardItemsArray)
    {

        $data = [];
        foreach ($giftCardItemsArray as $item) { 
            $data[] = self::find()
            ->where(['gift_card_number' => $item])
            ->one();   
        }

        return $data;
    }

    
    public static function getGiftCardNumberById($giftCardId)
    {

        if(empty($giftCardId)){
            
            return false;
        }

        $data = self::find()
        ->where(['id' => $giftCardId])
        ->one();   
      

        return $data->gift_card_number;
    }


    public static function getGiftCardIdByNumber($giftCardNumber)
    {

        if(empty($giftCardNumber)){
            
            return false;
        }

        $data = self::find()
        ->where(['gift_card_number' => $giftCardNumber])
        ->one();   
      

        return $data->id;
    }
    
    public function afterFind()
    {
        // Date Format
        $attributes = ['initial_balance_date'];
        foreach ($attributes as $attribute) {
            if (isset($this->$attribute)) {
                $this->$attribute = Yii::$app->formatter->format($this->$attribute, ['date']);
            }
        }

        return parent::afterFind();
    }
    public static function getGiftCardRelationalData($giftCardId)
    {
        if(empty($giftCardId)){
            return false;
        }


        $modelGiftCardTransactionId = Transaction::find()
            ->select('id')
            ->andWhere(['purchasing_account_type' => CommonHelper::ACCOUNT_TYPE_GIFT_CARD])
            ->andWhere(['not in','transaction_doc_type' , CommonHelper::DOC_TYPE_INITIAL_TRANSACTION])
            ->andWhere(['org_id' => CommonHelper::getLoggedInUserOrgId()])
            ->andWhere(['purchasing_account_id' => $giftCardId])
            ->asArray()
            ->one();


        if($modelGiftCardTransactionId){
            return 1;
        }

        $modelGiftCardInboxTransactionId = InboxTransaction::find()
            ->select('id')
            ->andWhere(['purchasing_account_type' => CommonHelper::ACCOUNT_TYPE_GIFT_CARD])
            ->andWhere(['org_id' => CommonHelper::getLoggedInUserOrgId()])
            ->andWhere(['purchasing_account_id' => $giftCardId])
            ->asArray()
            ->one();


        if($modelGiftCardInboxTransactionId){
            return 1;
        }

        return 0;
    }

    public static function findGiftCardModelByCardNumberAndSupplierId($giftCardNumber, $supplierId)
    {
        $model = GiftCard::find()
            ->where(['gift_card_number' => $giftCardNumber])
            ->andWhere(['supplier_id' => $supplierId])
            ->andWhere(['org_id' => CommonHelper::getLoggedInUserOrgId()])
            ->one();

        if($model){
            return $model;
        }else {
            return new GiftCard();
        }


    }

    /**
    * before save data
    */
    public function beforeSave($insert)
    {
        // Date Time Format Change
        $dateAttributes = [
            'purchased_date', 'initial_balance_date'
        ];
        foreach ($dateAttributes as $attribute) {
            if (isset($this->$attribute)) {
                $this->$attribute = CommonHelper::changeDateFormat($this->$attribute, 'Y-m-d');
            }
        }

        if (parent::beforeSave($insert))
        {
            if ($this->isNewRecord)
            {
                $this->doc_id     = CommonHelper::getNextIncrementDocId(CommonHelper::DOC_TYPE_GIFT_CARD);
                $this->org_id     = CommonHelper::getLoggedInUserOrgId();
                $this->created_by = CommonHelper::getLoggedInUserId();
                $this->created_at = date('Y-m-d H:i:s');
            }
            else
            {
                $this->updated_by = CommonHelper::getLoggedInUserId();
                $this->updated_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        else return false;
    }
    public function afterSave($insert, $changedAttributes)
    {
        $changedGiftCardNumberAttributes = isset($changedAttributes['gift_card_number'])? $changedAttributes['gift_card_number'] : '';

        if($changedGiftCardNumberAttributes || $insert){
            //InboxTransaction

            $paymentItemsByPurchasingAccountId = InboxTransaction::find()
                ->select('id,order_id, purchasing_account_type,purchasing_account, purchasing_account_id')
                ->where(['purchasing_account_id' => null])
                ->andWhere(['purchasing_account_type' => CommonHelper::ACCOUNT_TYPE_GIFT_CARD])
                ->andWhere(['purchasing_account' => $this['gift_card_number']])
                ->andWhere(['org_id' => CommonHelper::getLoggedInUserOrgId()])
                ->asArray()
                ->all();

            if (!empty($paymentItemsByPurchasingAccountId)) {
                foreach ($paymentItemsByPurchasingAccountId as $paymentItem) {

                    $inboxOrderInfo = InboxOrder::getInboxOrderInfoById($paymentItem['order_id']);

                    $supplierId = $inboxOrderInfo['supplier_id'];

                    $paymentItem['purchasing_account_id'] = MatchingRule::getMatchingRulePurchasingAccountId($paymentItem['purchasing_account_type'], $paymentItem['purchasing_account'],$supplierId);

                    if($paymentItem['purchasing_account_id']){
                        Yii::$app->db->createCommand('UPDATE inv_inbox_transaction SET purchasing_account_id = "'.$paymentItem['purchasing_account_id'].'" WHERE id ="'.$paymentItem['id'].'"')->execute();
                    }

                }
            }
        }


        //Set Default Transaction Entry for Balance Calculation

        $modelGiftCardItem = Transaction::findInitialTransaction($this['doc_id'], CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT, CommonHelper::DOC_TYPE_INITIAL_TRANSACTION,  CommonHelper::ACCOUNT_TYPE_GIFT_CARD, $this['id']);

        $modelGiftCardItem->transaction_type =  CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT;
        $modelGiftCardItem->transaction_doc_type = CommonHelper::DOC_TYPE_INITIAL_TRANSACTION;
        $modelGiftCardItem->transaction_doc_id = $this['doc_id'];
        $modelGiftCardItem->transaction_date = $this['initial_balance_date'];
        $modelGiftCardItem->purchasing_account_type =  CommonHelper::ACCOUNT_TYPE_GIFT_CARD;
        $modelGiftCardItem->purchasing_account_id = $this['id'];

        //Save
        $transactionPaymentMethodInfo      = Transaction::saveTransactionInfo($modelGiftCardItem);
    }

}
