<?php
declare(strict_types=1);

namespace Gaiterjones\MessageManager\Model\MessageQueues\Manager;

use Magento\Framework\MessageQueue\PublisherInterface;

class Publisher
{
    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisher;

    /**
    * Publisher constructor.
    * @param Publisher $publisher
    */
    public function __construct(
        PublisherInterface $publisher
    ) {
        $this->publisher = $publisher;
    }

    /**
    * @param data
    */
    public function execute(string $topicName,array $data)
    {
        $this->publisher->publish($topicName, json_encode($data));
    }
}
