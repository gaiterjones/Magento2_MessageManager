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
        $message=json_decode($message,true);
        $action=$message['action'];
        $data=$message['data'];

        // Consumer Actions
        //
        switch ($action) {
            case 'sendmailtocustomer':
            $success=$this->sendMail->sendMailToCustomer($data['sendto'], $data['customerid'], $data['emailtemplate'], $data['storeid'], $data['sender']);
                echo 'sendmailtocustomer:'.$data['sendto']. ':'. ($success ? 'SUCCESS':'ERROR').PHP_EOL;
                    break;
            case 'anothercustomaction':
                    // custom action goes here...
                    break;
            default:
                print_r(json_decode($message)).PHP_EOL;
        }

    }
}
