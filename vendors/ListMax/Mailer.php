<?php
require_once('Zend/Mail.php');
require_once('Zend/Mail/Transport/Smtp.php');

class Mailer {

	private $transport;
	private $fromAddress;
	private $returnPath;
	private $replyTo;
	private $subject;
	private $tos          = array();
	private $ccs          = array();
	private $bccs         = array();
	private $values       = array();
	private $template     = "default_email";
	private $mode         = "mixed";

	public function __construct(){
	}

	public function addTo( $toAddress ) {
		$this->tos[] = $toAddress;
	}
	
	public function addCC( $ccAddress ) {
		$this->ccs[]  = $ccAddress;
	}
	
	public function addBCC( $bccAddress ) {
		$this->bccs[] = $bccAddress;
	}
	
	public function setFrom( $fromAddress ) {
		$this->fromAddress = $fromAddress;
	}

	public function setMode( $mode ) {
		$this->mode = $mode;
	}

	public function setReturnPath( $returnPath ) {
		$this->returnPath = $returnPath;
	}

	public function setReplyTo( $replyTo ) {
		$this->replyTo = $replyTo;
	}

	public function setSubject( $subject ) {
		$this->subject = $subject;
	}

	public function setTemplate( $template ) {
		$this->template = $template;
	}

	public function set( $key , $value = null ) {
		
		if( is_array( $key ) && $value === null ){
			$this->values = array_merge( $this->values , $key );
		}else{
			$this->values[ $key ] = $value;
		}
		
	}

    public function parse( ){
        
        //Check that the file exists
        if( !file_exists( func_get_arg(0) ) && !is_file( func_get_arg(0) ) ){
            return false;
        }
                
        //Load the variables into function context
		extract( $this->values );
		
		//Parse the php file
		ob_start();
        include( func_get_arg(0) );
        $content = ob_get_clean();
        return $content;
            
    }
    
	public function send( $name ) {

        if (ENVIRONMENT == 'dev') {
            $this->tos         = array();
            $this->tos[]       = "daniel@listmax.com";
            $this->ccs         = array();
            $this->bccs        = array();
            //$this->fromAddress = "daniel@listmax.com";
            $this->mode        = "mixed";
        }

//		$this->transport = new Zend_Mail_Transport_Smtp( SMTP );
//		Zend_Mail::setDefaultTransport($this->transport);

		$zendMail = new Zend_Mail('UTF-8');			
		$zendMail->setFrom( $this->fromAddress );
		$zendMail->setReturnPath( $this->returnPath );
		$zendMail->setSubject( $this->subject );

        foreach( $this->tos as $to ){
			$zendMail->addTo( $to );
		}

        foreach( $this->ccs as $cc ){
			$zendMail->addCc( $cc );
		}
		
        foreach( $this->bccs as $bcc ){
			$zendMail->addBcc( $bcc );
		}

        //Get the path to the emails
        $email_path = dirname( __FILE__ ).DIRECTORY_SEPARATOR . "Emails" . DIRECTORY_SEPARATOR;

        //Get text content
		if( $this->mode == 'text' || $this->mode == 'mixed' ){
    		$TXTCONTENT  = $this->parse( $email_path . $name.".txt.php");
    		$zendMail->setBodyText( $TXTCONTENT );    		
		}

        //Get the html content        
		if( $this->mode == 'html' || $this->mode == 'mixed' ){
            $HTMLCONTENT = $this->parse( $email_path . $name.".html.php" );
    		$zendMail->setBodyHtml( $HTMLCONTENT );
		}
	    
        if( !empty( $this->replyTo ) ){
    		$zendMail->addHeader( "Reply-To" , $this->replyTo );
        }
		// sends email
		return $zendMail->send();
	}

}
?>
