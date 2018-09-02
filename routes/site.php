<?php  
use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Products;


$app->get('/', function() {

	$page=new Page();

	$products= new Products();


	$page->setTpl('index',[
            "products"=>Products::checkList($products->listAll())
	]);

          exit();
    
});


$app->get('/category/:category/:idcategory',function($categoryName,$idcategory){

     $category=new Category();

     $category->get((int)$idcategory);

     $page=new Page();

     $page->setTpl('category',array(

         "category"=>$category->getValues(),
         'products'=>Products::checkList($category->getProducts())

     )); 


});