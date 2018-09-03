<?php  
use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Products;
use \Hcode\Model\Cart;


$app->get('/', function() {

	$page=new Page(['data'=>['active'=>'home']]);

	$products= new Products();


	$page->setTpl('index',[
            "products"=>Products::checkList($products->listAll())
	]);

   
    
});


$app->get('/category/:category/:idcategory',function($categoryName,$idcategory){

     $pageAltual= (isset($_GET['page']))?(int)$_GET['page']:1;

     $category=new Category();

     $category->get((int)$idcategory);
     $pagination=$category->getProductsPage($pageAltual);



     $pages=[];
     for ($i=1; $i <=$pagination['pages']; $i++)
     { 
        array_push($pages,[
            'link'=>'/category/'.$categoryName.'/'.$category->getidcategory().'?page='.$i,
            'page'=>$i,

        ]);
     }
    

     $page=new Page(['data'=>['active'=>'product']]);
     $page->setTpl('category',array(

         "category"=>$category->getValues(),
         'products'=>$pagination['data'],
         'pages'=>$pages,
         'pageAtual'=>$pageAltual

     )); 



}); 


$app->get('/products/:desurl',function($desurl){
    
    $product= new Products();

    $product->getFromURL($desurl);

    $page=new Page(['data'=>['active'=>'home']]);

    $page->setTpl('product-detail',[
            "product"=>$product->getValues(),
            'categories'=>$product->getCategories()
    ]);


});

$app->get('/cart',function(){

    $cart = Cart::getFromSession();

    $page=new Page(['data'=>['active'=>'cart']]);

    $page->setTpl('cart');
    
     
});

$app->get('/cart/:idproduct/add',function($idproduct){

    $product = new Products();

    $product->get((int)$idproduct);

    $cart= Cart::getFromSession();

    $cart->addProdut($product);
    
    header('location /cart');
    exit;

});


$app->get('/cart/:idproduct/minius',function($idproduct){

    $product = new Products();

    $product->get((int)$idproduct);

    $cart= Cart::getFromSession();

    $cart->removeProdut($product);
    
    header('location /cart');
    exit;

});


$app->get('/cart/:idproduct/remove',function($idproduct){

    $product = new Products();

    $product->get((int)$idproduct);

    $cart= Cart::getFromSession();

    $cart->removeProdut($product,true);
    
    header('location /cart');
    exit;

});



