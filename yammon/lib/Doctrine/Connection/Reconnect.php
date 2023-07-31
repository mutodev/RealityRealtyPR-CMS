<?php

class Doctrine_Connection_Reconnect extends Doctrine_EventListener
{
    public function preError( Doctrine_Event $event )
    {
        if (Configure::read('doctrine.reconnect', false) === false) {
            return;
        }

        if ($event->getCode() === Doctrine_Event::CONN_ERROR) {

            $reconnectBackoff     = 0;
            $reconnectBackoffTime = 500000; //0.1 second
            $reconnectBackoffMax  = 30000000; //30 seconds

            do {
                try {

                    echo "<warn> Database connection problem\n";

                    $reconnectBackoff += $reconnectBackoffTime;

                    usleep(min($reconnectBackoff, $reconnectBackoffMax));

                    $event->getInvoker()->close();
                    $event->getInvoker()->connect();
                    $event->getInvoker()->execute( 'SET NAMES utf8mb4 COLLATE UTF8MB4_UNICODE_CI' );  //TODO: CHECK IF NEEDED
                    $event->getInvoker()->execute( 'SET time_zone="+00:00"' );
                }
                catch (Doctrine_Connection_Exception $e) {}

            } while(!$event->getInvoker()->isConnected());
        }
    }
}
