<?php 
  namespace app\models;
  use yii\db\ActiveRecord;
  use Yii;
  use yii\behaviors\TimestampBehavior;

  class Cart extends ActiveRecord
  {
    public static function fbsql_tablename()
    {
      return "{{%cart}}";
    }

    public function rules()
    {
      return [
        [['productid','productnum','userid','price'],'required'],
        ['createtime','safe']
      ];
    }

      public function behaviors()
      {
          return [
              [
                  'class' => TimestampBehavior::className(),
                  'createdAtAttribute' => 'createtime',
                  'updatedAtAttribute' => 'updatetime',
                  'attributes' => [
                      ActiveRecord::EVENT_BEFORE_INSERT => ['createtime', 'updatetime'],
                      ActiveRecord::EVENT_BEFORE_UPDATE => ['updatetime'],
                  ]
              ]
          ];
      }
  }




