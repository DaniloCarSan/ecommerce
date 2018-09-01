<?php 
use \Hcode\Page;
use \Hcode\Model\User;

$app->get('/', function() {

	$page=new Page();

	$page->setTpl('index');

    exit();
    
});