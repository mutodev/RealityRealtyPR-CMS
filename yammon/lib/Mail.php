<?php

    vendor('SwiftMailer');

    class Mail extends Collection{


        private $transport_mode   = null;
        private $smtp_host        = null;
        private $smtp_ssl         = null;
        private $smtp_port        = null;
        private $smtp_ssl_port    = null;
        private $smtp_username    = null;
        private $smtp_password    = null;
        private $sendmail_command = null;
        private $subject          = '';
        private $from             = '';
        private $from_name        = '';
        private $to               = array();
        private $cc               = array();
        private $bcc              = array();
        private $return_path      = null;
        private $reply_to         = "";
        private $receipt_to       = "";
        private $priority         = null;
        private $max_line_length  = 250;
        private $date             = null;
        private $text             = null;
        private $html             = null;
        private $text_file        = null;
        private $html_file        = null;
        private $text_layout      = null;
        private $html_layout      = null;
        private $batch            = false;
        private $attachments      = array();

        private $mailer           = null;
        private $message          = null;
        private $transport        = null;

        function __construct(){
            $this->message = Swift_Message::newInstance();
            $this->message->setMaxLineLength( $this->getMaxLineLength() );
        }

        function setBatchMode( $batch ){
            $this->batch = !!$batch;
        }

        function getBatchMode(){
            return $this->batch;
        }

        function setTransportMode( $mode ){
            $this->mailer         = null;
            $this->transport      = null;
            $this->transport_mode = $mode;
        }

        function getTransportMode(){
            if( empty( $this->transport_mode ) )
                return Configure::read( 'mail.transport' , 'mail' );
            else
                return $this->transport_mode;
        }

        function setSmtpOptions( $host = 'localhost ', $port = 25 , $username = '' , $password = '' , $ssl = false , $ssl_port = 465 ){
            $this->mailer         = null;
            $this->transport      = null;
            $this->smtp_host     = $host;
            $this->smtp_port     = $port;
            $this->smtp_username = $username;
            $this->smtp_password = $password;
            $this->smtp_ssl      = $ssl;
            $this->smtp_ssl_port = $ssl_port;
        }

        function getSmtpHost(){
            if( empty( $this->smtp_host ) )
                return Configure::read( 'mail.smtp.host' , 'localhost' );
            else
                return $this->smtp_host;
        }

        function getSmtpPort(){

            if( $this->smtpUsesSSL() ){

                if( empty( $this->smtp_ssl_port ) )
                    return Configure::read( 'mail.smtp.ssl_port' , 465 );
                else
                    return $this->smtp_ssl_port;

            }else{

                if( empty( $this->smtp_port ) )
                    return Configure::read( 'mail.smtp.port' , 25 );
                else
                    return $this->smtp_port;

            }

        }

        function getSmtpUsername(){
            if( empty( $this->smtp_username ) )
                return Configure::read( 'mail.smtp.username' , '' );
            else
                return $this->smtp_username;
        }

        function getSmtpPassword(){
            if( empty( $this->smtp_password ) )
                return Configure::read( 'mail.smtp.password' , '' );
            else
                return $this->smtp_password;
        }

        function smtpUsesSSL(){

            $this->mailer         = null;
            $this->transport      = null;

            if( $this->smtp_ssl === null )
                return Configure::read( 'mail.smtp.ssl' , false );
            else
                return $this->smtp_ssl;
        }

        function setSendMailCommand( $command  ){
            $this->mailer         = null;
            $this->transport      = null;
            $this->sendmail_command = $command;
        }

        function getSendMailCommand(){
            if( empty( $this->sendmail_command ) )
                return Configure::read( 'mail.sendmail.command' , '/usr/sbin/sendmail -bs' );
            else
                return $this->sendmail_command;
        }

        function setSubject( $subject ){
            $this->subject = $subject;
            $this->message->setSubject( $this->getSubject() );
        }

        function getSubject(){
            return $this->subject;
        }

        function setFrom( $from , $name = null ){

            if( is_array( $from ) ){
                $this->from      = array_shift( $from );
                $this->from_name = array_shift( $from );
            }else{
                $this->from      = $from;
                $this->from_name = $name;
            }

            $this->message->setFrom( $this->from , $this->from_name );
        }


        function getFrom(){
            return $this->from;
        }


        function getFromName(){
            return $this->from_name;
        }

        function addTo( $email , $name = null ){
            $this->to[] = array( $email , $name );
            $this->message->addTo( $email , $name );
        }

        function setTo( $email , $name = null ){
            $this->to = array();
            $this->message->setTo( array() );
            $this->addTo( $email , $name );
        }

        function getTo(){
            return $this->to;
        }

        function addCC( $email , $name = null ){
            $this->cc[] = array( $email , $name );
            $this->message->addCc( $email , $name );
        }

        function setCC( $email , $name = null ){
            $this->cc = array();
            $this->message->setCc( array() );
            $this->addCC( $email , $name );
        }

        function getCC(){
            return $this->cc;
        }

        function addBCC( $email , $name = null ){
            $this->bcc[] = array( $email , $name );
            $this->message->addBcc( $email , $name );
        }

        function setBCC( $email , $name = null ){
            $this->bcc = array();
            $this->message->setBcc( array() );
            $this->addBCC( $email , $name );
        }

        function getBCC(){
            return $this->bcc;
        }

        function setReplyTo( $address , $name = null ){
            $this->reply_to = array( $address => $name );
            $this->message->setReplyTo( $this->getReplyTo() );
        }

        function getReplyTo(){
            return $this->reply_to;
        }

        function setReadReceiptTo( $address ){
            $this->receipt_to = array( $address => '' );
            $this->message->setReadReceiptTo( $this->receipt_to );
        }

        function getReadReceiptTo(){
            return $this->receipt_to;
        }

        function setReturnPath( $return_path ){
            $this->return_path = $return_path;
            $this->message->setReturnPath( $return_path );
        }

        function getReturnPath(){
            return $this->return_path;
        }

        function setDate( $date ){
            $this->date = $date;
            $this->message->setDate( $date );
        }

        function getDate(){
            return $this->date;
        }

        function setPriority( $priority ){
            $this->priority = $priority;
            $this->message->setPriority( $this->getPriority() );
        }

        function getPriority(){
            return $this->priority;
        }

        function setMaxLineLength( $len ){
            $this->max_line_length = $len;
            $this->message->setMaxLineLength( $len );
        }

        function getMaxLineLength(){
            return $this->max_line_length;
        }

        function setText( $text ){
            $this->text = $text;
            $this->updateBody();
        }

        function setTextFile( $file ){
            $this->text_file = $file;
            $this->updateBody();
        }

        function setHtml( $html ){
            $this->html = $html;
            $this->updateBody();
        }

        function setHtmlFile( $file ){
            $this->html_file = $file;
            $this->updateBody();
        }

        function getHtml(  ){

            if( $this->html ){
                return $this->html;
            }elseif( $this->html_file ){

                $view = new View();
                $view->set( $this->toArray() );
                $content = $view->render( $this->html_file );

                if( Layout::isLayout('email-html') ){
                    $layout = new Layout();
                    $layout->set( $view->toArray() );
                    $layout->setLayout( "email-html" );
                    $layout->setContent( $content );
                    $content = $layout->render();
                }

                return $content;

            }

            return null;

        }

        function getText(  ){

            if( $this->text ){
                return $this->text;
            }elseif( $this->text_file ){

                $view = new View();
                $view->set( $this->toArray() );
                $content = $view->render( $this->html_file );

                if( Layout::isLayout('email-html') ){
                    $layout = new Layout();
                    $layout->set( $view->toArray() );
                    $layout->setLayout( "email-txt" );
                    $layout->setContent( $content );
                    $content = $layout->render();
                }

                return $content;

            }

            return null;

        }

        function attach( $path , $filename = null ){

            if( $filename === null )
                $filename = basename( $path );

            $this->attachments[] = array( $path => $filename );


            $this->message->attach( Swift_Attachment::fromPath( $path )->setFileName( $filename ) );
        }

        function send(){

            $mailer  = $this->getMailer();

            //Send the message
            if( $this->batch ){
                $result = $mailer->batchSend( $this->message );
            }else{
                $result = $mailer->send( $this->message );
            }

            return $result;
        }

        protected function updateBody(  ) {

            $text = $this->getText( );
            $html = $this->getHtml();

            if( $text === null && $html === null ){
                $this->message->setBody( "" , 'text/plain' );
            }elseif( $text !== null && $html === null ){
                $this->message->setBody( $text , 'text/plain' );
            }elseif( $text === null && $html !== null ){
                 $this->message->setBody( $html , 'text/html' );
            }elseif( $text !== null && $html !== null ){
                $this->message->setBody( $text , 'text/plain' );
                $this->message->addPart( $html , 'text/html' );
            }
        }

        protected function getMailer(  ) {

            if ( $this->mailer )
                return $this->mailer;
            else
                return $this->mailer = Swift_Mailer::newInstance( $this->getTransport() );
        }

        protected function getTransport(){

            if( $this->transport ){
                return $this->transport;
            }

            //Get the transport
            $transport_mode = $this->getTransportMode();
            if( $transport_mode == "smtp" ){

                $transport = Swift_SmtpTransport::newInstance();
                $transport->setHost( $this->getSmtpHost() );
                $transport->setPort( $this->getSmtpPort() );
                $transport->setUsername( $this->getSmtpUsername() );
                $transport->setPassword( $this->getSmtpPassword() );

                if( $this->smtpUsesSSL() ){
                    $transport->setEncryption('ssl');
                }

                return $transport;
            }

            if( $transport_mode == "sendmail" ){
                $transport = Swift_SendmailTransport::newInstance( $this->getSendMailCommand() );
                return $transport;
            }

            $transport = Swift_MailTransport::newInstance();
            return $transport;


        }



    }


