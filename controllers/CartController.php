<?php 

namespace app\controllers;
use app\controllers\CommonController;
use app\models\User;
use app\models\Cart;
use app\models\Product;

use Yii;

class CartController extends CommonController{
  public $layout = "layout2";
  protected $mustlogin = ['index', 'mod', 'add', 'del'];

    /**
     * 购物车商品展示页面
     * @return string
     */
  public function actionIndex()
  {

    $userid = Yii::$app->user->id;
    $cart = Cart::find()->where('userid = :uid',[':uid'=>$userid])->asArray()->all();
    $data = [];
    // 根据productid进行查询
    foreach ($cart as $k=>$pro) {    // 
      $product = Product::find()->where('productid = :pid',[':pid'=>$pro['productid']])->one();
      $data[$k]['cover'] = $product->cover;
      $data[$k]['title'] = $product->title;
      $data[$k]['productnum'] = $pro['productnum'];
      $data[$k]['price'] = $pro['price'];
      $data[$k]['productid'] = $pro['productid'];
      $data[$k]['cartid'] = $pro['cartid'];
    }
    return $this->render("index",['data'=>$data]);
  }

  public function actionAdd()    // 将商品加入购物车,分两种加入方式
  {
     $userid = Yii::$app->user->id;
    if(Yii::$app->request->isPost) {      // 商品详情页
      $post = Yii::$app->request->post();
        //var_dump($post);die;
      $num = Yii::$app->request->post()['productnum'];
      $data['Cart'] = $post;
      $data['Cart']['userid'] = $userid;
    }

    if(Yii::$app->request->isGet) {     //商品列表页    这里页面接收get过来productid
      $productid = Yii::$app->request->get('productid');
      $model = Product::find()->where('productid = :pid',[':pid'=>$productid])->one();
      $price = $model->issale ? $model->saleprice : $model->price;
      $num = 1;
      $data['Cart'] = ['productid'=>$productid,'productnum'=>$num,'price'=>$price,'userid'=>$userid];
    }

    if(!$model = Cart::find()->where('productid = :pid and userid = :uid',[':pid'=>$data['Cart']['productid'], ':uid'=>$data['Cart']['userid']])->one()){  // 有该商品数量更新，没有插入
      $model = new Cart;
    } else {
      $data['Cart']['productnum'] = $model->productnum +$num;
    }
    $data['Cart']['createtime'] = time();
    $model->load($data);
    $model->save();    // cart::find产生就更新，new cart就创建
    return $this->redirect(['cart/index']);     
  }


    /**
     * 修改购物车商品数量
     */
  public function actionMod()
  {
    $cartid = Yii::$app->request->get("cartid");
    $productnum = Yii::$app->request->get("productnum");
    Cart::updateAll(['productnum' =>$productnum],'cartid= :cid',[':cid'=>$cartid]);
  }

  public function actionDel()   // 删除购物车商品
  {
    $cartid = Yii::$app->request->get("cartid");
    Cart::deleteAll('cartid = :cid',[':cid'=>$cartid]);
    return $this->redirect(['cart/index']);
  }


}
