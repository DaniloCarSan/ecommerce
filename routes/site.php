<?php  
use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\Model\Products;

$app->get('/', function() {

	$page=new Page();

	$products= new Products();


	$page->setTpl('index',[
            "products"=>Products::checkList($products->listAll())
	]);

          exit();
    
});