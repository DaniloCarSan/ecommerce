<?php 
use \Hcode\PageAdmin;
use \Hcode\Model\User;


$app->get('/admin', function() {

    User::verifyLogin((bool)$_SESSION['User']['inadmin']);

	$page=new PageAdmin();

	$page->setTpl('index');

    exit();
    
}); 

$app->get('/admin/login', function() {


	$page=new PageAdmin([
		'header'=>false,
		'footer'=>false

	]);
	$page->setTpl('login'); 

    exit();
    
});

$app->post('/admin/login', function() {

	User::login($_POST['login'],$_POST['password']);
 
	header("location:/admin");

	exit();
    
});

$app->get('/admin/logout', function() {

	User::logout();

	header("location:/admin/login");

	exit();  

});