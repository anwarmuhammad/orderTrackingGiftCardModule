<?php

namespace unlock\modules\giftcard\models;

use unlock\modules\purchaseorder\models\Transaction;
use Yii;
use yii\helpers\Html;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use unlock\modules\core\helpers\CommonHelper;

/**
 * This is the model class for table "{{%gift_card_payment}}".
 *
 * @property string $id
 * @property integer $org_id
 * @property string $doc_id
 * @property string $seller_id
 * @property string $seller_account_id
 * @property string $gift_card_ids
 * @property string $price
 * @property string $gift_card_pay_date
 * @property string $cash_back_website_id
 * @property string $cash_back_website_rate
 * @property string $debit_amount
 * @property string $note
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $status
 */
class GiftCardPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gift_card_payment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['seller_id', 'seller_account_id', 'price'], 'required'],
            [['org_id', 'seller_id', 'cash_back_website_id', 'created_by', 'updated_by', 'status'], 'integer'],//seller_account_id
            [['cash_back_website_rate', 'price'], 'number','min' => 1],
            [['gift_card_pay_date', 'created_at', 'updated_at'], 'safe'],
            [['doc_id'], 'string', 'max' => 100],
            [['note', 'gift_card_ids'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'org_id' => Yii::t('app', 'Org ID'),
            'doc_id' => Yii::t('app', 'Doc ID'),
            'seller_id' => Yii::t('app', 'Seller'),
            'seller_account_id' => Yii::t('app', 'Seller Account'),
            'gift_card_ids' => Yii::t('app', 'Gift Card Number'),
            'price' => Yii::t('app', 'Price'),
            'gift_card_pay_date' => Yii::t('app', 'Pay Date'),
            'cash_back_website_id' => Yii::t('app', 'Cash Back Website'),
            'cash_back_website_rate' => Yii::t('app', 'Cash Back Rate'),
            'note' => Yii::t('app', 'Note'),
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
    public function getGiftCardPaymentItems()
    {
        return $this->hasMany(GiftCardPaymentItem::className(), ['gift_card_payment_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionPaymentMethods()
    {
        return $this->hasMany(Transaction::className(), ['transaction_doc_id' => 'doc_id'])
            ->andOnCondition(['transaction_type' =>  CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT]);
    }

    /**
    * before save data
    */
    public function beforeSave($insert)
    {
        // Date Time Format Change
        $dateAttributes = [
            'gift_card_pay_date',
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
                $this->doc_id     = CommonHelper::getNextIncrementDocId(CommonHelper::DOC_TYPE_GIFT_CARD_PAYMENT);
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

    public static function saveGiftCardPayment($model)
    {
        if (!isset($model)) {
            throw new Exception(Yii::t('app', '<p><b>Gift Card Payment</b></p><p>data not save</p>'));
        }
       

        if (!$model->save()) {
            $errorMessagePrefix = '<p><b>Purchasing Account</b></p>';
            throw new Exception(Yii::t('app', $errorMessagePrefix . Html::errorSummary($model)));
        }

        return $model;
    }
}
