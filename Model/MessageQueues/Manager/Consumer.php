<?php

declare(strict_types=1);

namespace Gaiterjones\MessageManager\Model\MessageQueues\Manager;
use Gaiterjones\MessageManager\Model\SendMail;
use Gaiterjones\MessageManager\Model\PushNotification;

/**
 * Class Consumer
 * @package Gaiterjones\MessageManager\Model\MessageQueues\Manager\Consumer
 */
class Consumer
{
    private $sendMail;
    private $pushNotification;

    /**
    * Consumer constructor.
    */
    public function __construct(
        SendMail $sendMail,
        PushNotification $pushNotification
    ) {
        $this->sendMail = $sendMail;
        $this->pushNotification = $pushNotification;
    }

    public function processMessage(string $message)
    {
        $now = new \DateTime('NOW', new \DateTimeZone('UTC'));
        $timestamp=$now->format('F d Y H:i:s'). ' UTC:';

        $message=json_decode($message,true);

        if (isset($message['action']) && isset($message['data']))
        {
            $action=$message['action'];
            $data=$message['data'];

            // Consumer Actions
            //
            switch ($action) {
                case 'sendmailtocustomer':
                $success=$this->sendMail->sendMailToCustomer($data['emailtemplate'], $data['storeid'], $data['sender'],$data['sendto'], $data['customerid']);
                    echo $timestamp.'sendmailtocustomer:'.$data['sendto']. ':'. ($success ? 'SUCCESS':'ERROR').PHP_EOL;
                        break;
                case 'anothercustomaction':
                        // custom action goes here...
                        break;
                case 'sendpushnotification':
                    $success=$this->pushNotification->message($data['message']);
                    echo $timestamp.'sendpushnotification:'.$data['message']. ':'. ($success ? 'SUCCESS':'ERROR').PHP_EOL;
                    break;
                case 'newordernotification':
                    $success=$this->pushNotification->message($data['message']);
                    echo $timestamp.'newordernotification:'.$data['message']. ':'. ($success ? 'SUCCESS':'ERROR').PHP_EOL;
                    break; 
                case 'newcustomernotification':
                    $success=$this->pushNotification->message($data['message']);
                    echo $timestamp.'newcustomernotification:'.$data['message']. ':'. ($success ? 'SUCCESS':'ERROR').PHP_EOL;
                    break;                    
                case 'anothercustomaction':
                    // custom action goes here...
                    break;
                default:
                    // no valid action found...
                    echo 'NO VALID ACTION FOUND:'.$timestamp.json_encode($message).PHP_EOL;
            }
        }

    }
}
