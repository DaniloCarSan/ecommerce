<?php 
namespace Hcode\Model;
use Hcode\DB\Sql;
use Hcode\Model;
use Hcode\Mailer;
class User  extends Model {
	const SESSION="User";
	const SECRET='2k7sd0p2k9f8k5u5ssm598d00h3x7am3';

	public static function getFromSession()
	{
		$user= new User();

		if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser']>0)
		{
			$user->setData($_SESSION[User::SESSION]);
			
		}

	     return $user;
	}


	public static function checkLogin($inadmin=true)
	{
		if ( !isset($_SESSION[User::SESSION])
	         	||
	         	!$_SESSION[User::SESSION]
	         	||
	         	!(int)$_SESSION[User::SESSION]['iduser']>0
	         	)
		{
			return false;
		}
		else
		{

              if ($inadmin===true  && (bool)$_SESSION[User::SESSION]['inadmin']===true)
              {

              	return true;
              
              }
              else if($inadmin===false)
              {

              	 return true;

              }
              else
              {
              	  return false;
              }
		}

	}



	public static function login($login,$password){

		$sql= new  Sql;
		$results=$sql->select("SELECT iduser,idperson,inadmin,despassword,desperson FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE deslogin=:LOGIN",array(
           ":LOGIN"=>$login
		));

		if (count($results)===0)
		{
			throw new \Exception("Usuario ou senha invalidos");	
		}

		$data=$results[0];

		if (password_verify($password,$data['despassword']))
		{

			$user = new User();
			$user->setData($data);
               $user->checkPhoto(); 
			$_SESSION[User::SESSION]=$user->getValues();
  		    
  		    return $user;
			
		}
		else
		{
			throw new \Exception("Usuario ou senha invalidos");	
		}

	}

	public static function verifyLogin($inadmin=true){
         
	         if (!User::checkLogin($inadmin))
	         {
	         	header('location:/admin/login');
	         	exit;
	         }
	} 

	public static function logout(){
		$_SESSION[User::SESSION]=NULL;
	}


	public static function listAll(){

		$sql= new Sql;
        return  $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson)  ORDER BY b.desperson");
	}

	public static function checkList($list){
            
            foreach ($list as &$row) {
            	 
                 $p=new User;
                 $p->setData($row);
                 $row=$p->getValues();

            }

            return $list;

	}



	 public function save(){
        $sql= new Sql;
		$results=$sql->select("CALL sp_users_save( :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
			          ":desperson"=>$this->getdesperson(),
			          ":deslogin"=>$this->getdeslogin(),
			          ":despassword"=>password_hash($this->getdespassword(),PASSWORD_DEFAULT,['cost'=>12]),
			          ":desemail"=>$this->getdesemail(),
			          ":nrphone"=>$this->getnrphone(),
			          ":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);


	}

	public function get($iduser){

        $sql= new Sql;

        $results=$sql->select("SELECT * FROM tb_users a INNER JOIN  tb_persons b USING(idperson)  WHERE a.iduser=:iduser",array(
        	':iduser'=>$iduser
        ));

	    $this->setData($results[0]);


	}


	public function update(){

	    $sql= new Sql;

		$results=$sql->select("CALL sp_usersupdate_save( :iduser,:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",array(
			           ":iduser"=>$this->getiduser(),
			          ":desperson"=>$this->getdesperson(),
			          ":deslogin"=>$this->getdeslogin(),
			          ":despassword"=>password_hash($this->getdespassword(),PASSWORD_DEFAULT,['cost'=>12]),
			          ":desemail"=>$this->getdesemail(),
			          ":nrphone"=>$this->getnrphone(),
			          ":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}


	public function  delete(){
         
	    $sql= new Sql;

         $sql->query("CALL sp_users_delete(:iduser)",array(
        	":iduser"=>$this->getiduser()
        ));

         $image=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.
				                    "res".DIRECTORY_SEPARATOR.
				                   "site".DIRECTORY_SEPARATOR.
				                    "img".DIRECTORY_SEPARATOR.
				                "user".DIRECTORY_SEPARATOR.
				                    $this->getiduser().'.jpg';

		if (file_exists($image))
		{
	            unlink($image);
		}

        
	}

	public static function getForgot($email){

	    $sql= new Sql;

	    $results=$sql->select("SELECT * FROM `tb_persons` a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email",array(
           ':email'=>$email
	    ));


	    if (count($results)===0)
	    {

	     	throw new \Exception("Não foi possível  recuperar a senha !");
	   
	    }
	    else
	    { 
           $data=$results[0];

           $results2=$sql->select("CALL sp_userspasswordsrecoveries_create(:iduser,:desip)",array(
            ':iduser'=>$data['iduser'],
            ':desip'=>$_SERVER['REMOTE_ADDR']
           ));

           if (count($results2)===0)
           {
           	
	     	throw new \Exception("Não foi possível  recuperar a senha !");

           }
           else
           {

            $dataRecovery=$results2[0];

            $code=  base64_encode(openssl_encrypt($dataRecovery['idrecovery'],'AES-128-ECB',User::SECRET));

            $link=BASE_URL."admin/forgot/reset?code=$code";
            exit;
            $mailer= new Mailer($data['desemail'],$data['desperson'],'Redefinir senha','forgot',array(
               'name'=>$data['desperson'],
               'link'=>$link
            ));

            $mailer->send();

            return $data;

           }

	    }
       

	}

	public static function  validForgotDecrypt($code){

	    $sql= new Sql;

		$idrecovery= openssl_decrypt(base64_decode($code),'AES-128-ECB',User::SECRET);

		$results=$sql->select("SELECT * FROM `tb_userspasswordsrecoveries` a 
							   INNER JOIN tb_users b USING(iduser)
							   INNER JOIN tb_persons c USING(idperson)
							   WHERE
							   a.idrecovery=:idrecovery
							   AND 
							   a.dtrecovery is NULL
							   AND 
							   DATE_ADD(a.dtregister,INTERVAL 1 HOUR)>=NOW()

					  ",array(
					  	":idrecovery"=>$idrecovery
					  ));

	    if (count($results)===0){

	    	throw new \Exception("Não foi possível recuperar a senha !");
	    	
	    }
	    else
	    {

           return $results[0];

	    }


	}

	public static function setForgotUsed($idrecovery){

	    $sql= new Sql;

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery=NOW() WHERE  idrecovery=:idrecovery",array(
			":idrecovery"=>$idrecovery
		));

	}

	public function setPassword($password){

		$sql= new Sql;

		$sql->query("UPDATE tb_users SET despassword=:password WHERE iduser=:iduser",array(
         ':password'=>password_hash($password,PASSWORD_DEFAULT,['cost'=>12]),
         ':iduser'=>$this->getiduser()
		));
 
	}

	public function setPhoto($file)
	{

 		$extension=explode('.',$file['name']); 
                    $extension=end($extension);
 		

 		switch ($extension)
 		{
 			case 'jpg':
 			case 'jpeg':
 			$image=imagecreatefromjpeg($file['tmp_name']);
 			break;

 			case 'gif':
 			$image=imagecreatefromgif($file['tmp_name']);
 			break;

 			case 'png':
 			$image=imagecreatefrompng($file['tmp_name']);	
 			break;
 			
 			
 		}

 		$dist=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.
				                    "res".DIRECTORY_SEPARATOR.
				                   "site".DIRECTORY_SEPARATOR.
				                    "img".DIRECTORY_SEPARATOR.
				                "user".DIRECTORY_SEPARATOR.
				                    $this->getiduser().'.jpg';

 		imagejpeg($image,$dist,60);
 		imagedestroy($image);
 		$this->checkPhoto();

	}

		public function checkPhoto()
	{

		 if (file_exists(
				$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.
				                    "res".DIRECTORY_SEPARATOR.
				                   "site".DIRECTORY_SEPARATOR.
				                    "img".DIRECTORY_SEPARATOR.
				                "user".DIRECTORY_SEPARATOR.
				                    $this->getiduser().'.jpg'
			      )
		    )
		 {
			
		          $url= "/res/site/img/user/".$this->getiduser().'.jpg';

		 } 
		 else
		 {
                         
		          $url= "/res/site/img/user.png";


		 }

		return  $this->setdesphoto($url);


	} 

	public  function getValues()
	{

	     $this->checkPhoto();

		$values=parent::getValues();

		return $values;     			

	}

}