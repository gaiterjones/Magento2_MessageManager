<?php

declare(strict_types=1);

namespace Gaiterjones\MessageManager\Model\MessageQueues\Manager;
use Gaiterjones\MessageManager\Model\SendMail;

/**
 * Class Consumer
 * @package Gaiterjones\MessageManager\Model\MessageQueues\Manager\Consumer
 */
class Consumer
{
    private $sendMail;

    /**
    * Consumer constructor.
    */
    public function __construct(
        SendMail $sendMail
    ) {
        $this->sendMail = $sendMail;
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
                default:
                    echo $timestamp.json_encode($message).PHP_EOL;
            }
        }

    }
}
