<?php

namespace unlock\modules\giftcard\models;

use Yii;
use yii\base\Model;
use unlock\modules\core\data\ActiveDataProvider;
use unlock\modules\giftcard\models\GiftCard;
use unlock\modules\core\helpers\CommonHelper;

/**
 * GiftCardSearch represents the model behind the search form about `unlock\modules\giftcard\models\GiftCard`.
 */
class GiftCardSearch extends GiftCard
{
    public $balance;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'org_id', 'created_by', 'updated_by'], 'integer'],
            [['balance', 'doc_id', 'supplier_id', 'gift_card_seller_account_id', 'gift_card_seller_id', 'gift_card_title', 'gift_card_number', 'gift_card_pin', 'created_at', 'updated_at', 'status'], 'safe'],
            [['price', 'value', 'discount_rate'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = GiftCard::find();

        // add conditions that should always apply here
        $pageSize = isset($params['per-page']) ? intval($params['per-page']) : CommonHelper::GRID_PER_PAGE;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'gift_card_number'=>SORT_ASC,
                    // 'created_at' => SORT_DESC,
                    //'title' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            /*'id' => $this->id,*/
            'org_id' => $this->org_id,
       /*     'price' => $this->price,
            'value' => $this->value,
            'discount_rate' => $this->discount_rate,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
            'status' => $this->status,*/
        ]);

        $query->andFilterWhere(['supplier_id' => $this->supplier_id])
            ->andFilterWhere(['gift_card_seller_account_id' => $this->gift_card_seller_account_id])
            ->andFilterWhere(['gift_card_seller_id' => $this->gift_card_seller_id])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['gift_card_number' => $this->gift_card_number]);


        return $dataProvider;
    }
}
