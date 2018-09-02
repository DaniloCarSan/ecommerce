<?php 
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Products;

$app->get('/admin/categories',function(){

       User::verifyLogin();

       $categories=Category::listAll();

       $page=new PageAdmin();

       $page->setTpl('categories',array(

         "categories"=>$categories

       )); 

});


$app->get('/admin/categories/create',function(){

      User::verifyLogin();

       $page=new PageAdmin();

       $page->setTpl('categories-create'); 

});
 
$app->post('/admin/categories/create',function(){
       
       $category=new Category();

       $category->setData($_POST);

       $category->save();

       header('location: /admin/categories');
       exit;
    
});
 
$app->get('/admin/categories/:idcategory/delete',function($idcategory){

       User::verifyLogin();

       $category=new Category();

       $category->get((int)$idcategory);

       $category->delete();

       header('location: /admin/categories');
       exit;


});

$app->get('/admin/categories/:idcategory',function($idcategory){

       User::verifyLogin();

       $category=new Category();

       $category->get((int)$idcategory);

       $page=new PageAdmin();

       $page->setTpl('categories-update',array(
         "category"=>$category->getValues()
       )); 
});

$app->post('/admin/categories/:idcategory',function($idcategory){

       User::verifyLogin();

       $category=new Category();

       $category->get((int)$idcategory);
 
       $category->setData($_POST);

       $category->save();

       header('location: /admin/categories');
       exit;
 
}); 



$app->get('/admin/categories/:idcategory/products',function($idcategory){

       User::verifyLogin();

       $category=new Category();

       $category->get((int)$idcategory);

       $page=new PageAdmin();

       $page->setTpl('categories-products',array(

         "category"=>$category->getValues(),
         'productsRelated'=>$category->getProducts(),
         'productsNotRelated'=>$category->getProducts(false)

     )); 
     
});

$app->get('/admin/categories/:idcategory/products/:idproduct/add',function($idcategory,$idproduct){

       User::verifyLogin();

       $category=new Category();

       $category->get((int)$idcategory);
   

       $product= new Products;

       $product->get((int)$idproduct);
  
       $category->addProduct($product);

      
      header('location: /admin/categories/'.$idcategory.'/products');
      exit;
     
});



$app->get('/admin/categories/:idcategory/products/:idproduct/remove',function($idcategory,$idproduct){

       User::verifyLogin();

       $category=new Category();

       $category->get((int)$idcategory);

       $product= new Products;

       $product->get((int)$idproduct);

       $category->removeProduct($product);

      
      header('location: /admin/categories/'.$idcategory.'/products');
      exit;
     
});

