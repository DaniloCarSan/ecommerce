<?php 
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Products;

$app->get('/admin/products',function(){

	User::verifyLogin();

	$products=Products::listAll();

 	$page=new PageAdmin();

 	$page->setTpl("products",[
 		"products"=>$products
 	]);
});

$app->get('/admin/products/create',function(){

	User::verifyLogin();

 	$page=new PageAdmin();

 	$page->setTpl("products-create");

});

$app->post('/admin/products/create',function(){

	User::verifyLogin();

	$product= new Products();

          $_POST['desurl']=Slug($_POST['desproduct']);

	$product->setData($_POST);

	$product->save();

 	header('location: /admin/products');
 	exit;
 	
});

$app->get('/admin/products/:idproduct',function($idproduct){

	User::verifyLogin();

	$product= new Products();

	$product->get((int) $idproduct);

 	$page=new PageAdmin();

 	$page->setTpl("products-update",[
            'product'=>$product->getValues()
 	]); 

});
 
$app->post('/admin/products/:idproduct',function($idproduct){

	User::verifyLogin();

	$product= new Products();

	$product->get((int) $idproduct);

 	$product->setData($_POST);

 	$product->save();


          if (!empty($_FILES['desphoto']['name']) && !empty($_FILES['desphoto']['tmp_name']))
          {
              $product->setPhoto($_FILES['desphoto']);
          }
 	

 	header('location: /admin/products');
 	exit;

});



$app->get('/admin/products/:idproduct/delete',function($idproduct){

       User::verifyLogin();

       $product=new Products();

       $product->get((int)$idproduct);
       
       $product->delete();
 

       header('location: /admin/products');
       exit;

});



