<?php 
use \Hcode\PageAdmin; 
use \Hcode\Model\User;

$app->get('/admin/users',function(){

    User::verifyLogin();

    $user= new User(); 

	$page = new PageAdmin();

	$page->setTpl('users',array(
      "users"=>$user->checkList(User::listAll()) 
	));

    exit();


});

$app->get('/admin/users/create',function(){

    User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl('users-create');

    exit();
   
});

$app->get('/admin/users/:iduser/delete',function($iduser){

    User::verifyLogin();

    $user= new User();

    $user->get((int)$iduser);

    $user->delete();

    header('location:/admin/users');

    exit();
}); 

$app->get('/admin/users/:iduser',function($iduser){
    User::verifyLogin();

	$page = new PageAdmin();

    $user= new User();

    $user->get((int)$iduser);


	$page->setTpl('users-update',array(

        'user'=>$user->getValues()

	));

  exit();

});

$app->post('/admin/users/create',function(){
    User::verifyLogin();

    $user= new User();

    $_POST['inadmin']=(isset($_POST['inadmin']))?1:0;

    $user->setData($_POST);

    $user->save();

    header('location:/admin/users');

    exit();

});

$app->post('/admin/users/:iduser',function($iduser){

    User::verifyLogin();

    $user= new User();

    $_POST['inadmin']=(isset($_POST['inadmin']))?1:0;

    $user->get((int)$iduser);

    $user->setData($_POST);

    $user->update();


     if (!empty($_FILES['desphoto']['name']) && !empty($_FILES['desphoto']['tmp_name']))
          {

              $user->setPhoto($_FILES['desphoto']);
          }  


   
    header('location:/admin/users');
    exit();

});