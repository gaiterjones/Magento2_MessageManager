<?php

declare(strict_types=1);

namespace Gaiterjones\MessageManager\Model\MessageQueues\Manager;

/**
 * Class Consumer
 * @package Gaiterjones\MessageManager\Model\MessageQueues\Manager
 */
class Consumer
{
    /**
    * Consumer constructor.
    */
    public function __construct()
    {
    }

    public function processMessage(string $data)
    {

        // do something with message queue data
        //
        print_r(json_decode($data)).PHP_EOL;
    }
}
