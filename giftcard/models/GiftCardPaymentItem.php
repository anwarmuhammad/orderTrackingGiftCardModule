<?php

namespace unlock\modules\giftcard\models;

use Yii;
use yii\helpers\ArrayHelper;
use unlock\modules\core\helpers\CommonHelper;
use unlock\modules\usermanager\user\models\User;

/**
 * This is the model class for table "{{%gift_card_item}}".
 *
 * @property string $id
 * @property string $gift_card_payment_id
 * @property string $gift_card_id
 * @property string $amount
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 * @property integer $status
 *
 * @property GiftCard $giftCard
 * @property User $createdBy
 * @property User $updatedBy
 */
class GiftCardPaymentItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%gift_card_payment_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gift_card_payment_id', 'gift_card_id'], 'required'],
            [['gift_card_payment_id', 'org_id','gift_card_id', 'created_by', 'updated_by', 'status'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['gift_card_id'], 'exist', 'skipOnError' => true, 'targetClass' => GiftCard::className(), 'targetAttribute' => ['gift_card_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'gift_card_payment_id' => Yii::t('app', 'Gift Card Payment ID'),
            'org_id' => Yii::t('app', 'Org ID'),
            'gift_card_id' => Yii::t('app', 'Gift Card ID'),
            'amount' => Yii::t('app', 'Amount'),
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
    public function getGiftCard()
    {
        return $this->hasOne(GiftCard::className(), ['id' => 'gift_card_id']);
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

    /** Get All Active GiftCardPaymentItem Drop Down List*/
    public static function giftCardPaymentItemDropDownList($status = CommonHelper::STATUS_ACTIVE)
    {
        $model = self::find()
            ->where('status =:status', [':status' => $status])
            ->all();

        return  ArrayHelper::map($model, 'id', 'title');
    }

    /**
    * before save data
    */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert))
        {
            if ($this->isNewRecord)
            {
                $this->org_id     = CommonHelper::getLoggedInUserOrgId();
                $this->created_by = CommonHelper::getLoggedInUserId();
                $this->created_at = date('Y-m-d H:i:s');
            }
            else
            {
                $this->org_id     = CommonHelper::getLoggedInUserOrgId();
                $this->updated_by = CommonHelper::getLoggedInUserId();
                $this->updated_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        else return false;
    }
}
