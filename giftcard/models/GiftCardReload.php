<?php

namespace unlock\modules\giftcard\models;

use unlock\modules\purchaseorder\models\Transaction;
use Yii;
use yii\helpers\Html;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use unlock\modules\core\helpers\CommonHelper;

/**
 * This is the model class for table "{{%gift_card_reload}}".
 *
 * @property string $id
 * @property integer $org_id
 * @property string $doc_id
 * @property string $gift_card_id
 * @property string $seller_id
 * @property string $seller_account_id
 * @property string $value
 * @property string $price
 * @property string $discount_rate
 * @property string $cash_back_website_id
 * @property string $cash_back_website_rate
 * @property string $credit_amount
 * @property string $note
 * @property string $gift_card_reload_date
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $status
 *
 * @property TransactionPaymentMethod[] $transactionPaymentMethods
 */
class GiftCardReload extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gift_card_reload}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gift_card_id', 'seller_id', 'seller_account_id', 'value', 'price'], 'required'],
            [['org_id', 'seller_id', 'seller_account_id', 'gift_card_id', 'cash_back_website_id', 'created_by', 'updated_by', 'status'], 'integer'],
            [['created_at', 'updated_at', 'gift_card_reload_date'], 'safe'],
            [[ 'value', 'price'], 'number','min' => 1,],
            [['cash_back_website_rate', 'discount_rate'], 'number','min' => 0,],
            [['doc_id'], 'string', 'max' => 100],
            [['note'], 'string', 'max' => 255],
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
            'gift_card_id' => Yii::t('app', 'Gift Card Number'),
            'seller_id' => Yii::t('app', 'Seller'),
            'seller_account_id' => Yii::t('app', 'Seller Account'),
            'value' => Yii::t('app', 'Value'),
            'price' => Yii::t('app', 'Price'),
            'discount_rate' => Yii::t('app', 'Discount %'),
            'cash_back_website_id' => Yii::t('app', 'Cash Back Website'),
            'cash_back_website_rate' => Yii::t('app', 'Cash Back Rate'),
            'note' => Yii::t('app', 'Note'),
            'gift_card_reload_date' => Yii::t('app', 'Reload Date'),
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
    public function getTransactionPaymentMethods()
    {
        return $this->hasMany(Transaction::className(), ['transaction_doc_id' => 'doc_id'])
            ->andOnCondition(['transaction_type' =>  CommonHelper::TRANSACTION_TYPE_PAYMENT_ACCOUNT])
            ->andWhere(['or',
                ['purchasing_account_type' => CommonHelper::ACCOUNT_TYPE_PAY_PAL],
                ['purchasing_account_type' => CommonHelper::ACCOUNT_TYPE_CREDIT_CARD],
                ['purchasing_account_type' => CommonHelper::ACCOUNT_TYPE_DEBIT_CARD],
            ]);
    }

    /** Get All Active GiftCardReload Drop Down List*/
    public static function giftCardReloadDropDownList($status = CommonHelper::STATUS_ACTIVE)
    {
        $model = self::find()
            ->where('status =:status', [':status' => $status])
            ->all();

        return  ArrayHelper::map($model, 'id', 'id');
    }

    /**
    * before save data
    */
    public function beforeSave($insert)
    {
        // Date Time Format Change
        $dateAttributes = [
            'gift_card_reload_date'
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
                $this->doc_id     = CommonHelper::getNextIncrementDocId(CommonHelper::DOC_TYPE_GIFT_CARD_RELOAD);
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

    public static function saveGiftCardReload($model)
    {
        if (!isset($model)) {
            throw new Exception(Yii::t('app', '<p><b>Gift Card Reload</b></p><p>data not save</p>'));
        }

        if (!$model->save()) {
            $errorMessagePrefix = '<p><b>Purchasing Account</b></p>';
            throw new Exception(Yii::t('app', $errorMessagePrefix . Html::errorSummary($model)));
        }

        return $model;
    }
}
