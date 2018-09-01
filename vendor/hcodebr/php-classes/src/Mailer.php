<?php 
namespace Hcode;

use Rain\Tpl;

class Mailer{ 

	const USERNAME='danilocarsan@gmail.com';
	const PASSWORD='danilocarsan1';
	const NAME_FROM='Danilo Santos';

	private $email;

	public function __construct($toAddress,$toName,$subject,$tplName,$data=array()){

		   $config = array(
                            "base_url"	    => "http://www.hcodecommerce.com.br/", 
							"tpl_dir"       => $_SERVER['DOCUMENT_ROOT'].'/views/email/',
							"cache_dir"     => $_SERVER['DOCUMENT_ROOT']."/views-cache/",
							'debudg'=>false

						   );

			Tpl::configure( $config );

	        $tpl = new Tpl;

	        foreach ($data as $key => $value) {
	        	$tpl->assign($key,$value);
	        }

	        $html=$tpl->draw($tplName,true);

		    //Create a new PHPMailer instance
			$this->email = new \PHPMailer;

			//Tell PHPMailer to use SMTP
			$this->email->isSMTP();

			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$this->email->SMTPDebug = 0;

			//Ask for HTML-friendly debug output
			$this->email->Debugoutput = 'html';

			//Set the hostname of the mail server
			$this->email->Host = 'smtp.gmail.com';
			// use
			// $this->email->Host = gethostbyname('smtp.gmail.com');
			// if your network does not support SMTP over IPv6

			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$this->email->Port = 587;

			//Set the encryption system to use - ssl (deprecated) or tls
			$this->email->SMTPSecure = 'tls';

			//Whether to use SMTP authentication
			$this->email->SMTPAuth = true;

			//Username to use for SMTP authentication - use full email address for gmail
			$this->email->Username = Mailer::USERNAME;

			//Password to use for SMTP authentication
			$this->email->Password = Mailer::PASSWORD;

			//Set who the message is to be sent from
			$this->email->setFrom(Mailer::USERNAME,Mailer::NAME_FROM);

			//Set an alternative reply-to address
			// $this->email->addReplyTo('replyto@example.com', 'First Last');

			//Set who the message is to be sent to
			$this->email->addAddress($toAddress,$toName);

			//Set the subject line
			$this->email->Subject = $subject;

			//Read an HTML message body from an external file, convert referenced images to embedded,
			//convert HTML into a basic plain-text alternative body
			$this->email->msgHTML($html);

			//Replace the plain text body with one created manually
			$this->email->AltBody = 'This is a plain-text message body';

			//Attach an image file
			// $this->email->addAttachment('images/phpmailer_mini.png');

			
	} 

	public function send(){
		return $this->email->send();
	}
}