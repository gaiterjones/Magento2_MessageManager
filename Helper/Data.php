<?php

declare(strict_types=1);

namespace Gaiterjones\MessageManager\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\MessageQueue\Consumer\ConfigInterface as ConsumerConfigInterface;
use Gaiterjones\MessageManager\Model\MessageQueues\Manager\Publisher as Publisher;
use Gaiterjones\MessageManager\Model\MessageQueues\Config as MessageQueueConfig;

/**
 * MessageManager data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $logger;

    public $publisher;

    public $consumerConfig;

    public $config;

    public $scopeConfig;

    public function __construct(
        Context $context,
        LoggerInterface $loggerInterface,
        Publisher $publisher,
        ConsumerConfigInterface $consumerConfig,
        ScopeConfigInterface $scopeConfig,
        MessageQueueConfig $config
    ) {
        $this->logger = $loggerInterface;
        $this->publisher = $publisher;
        $this->consumerConfig = $consumerConfig;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        parent::__construct($context);
    }

    // get consumers
    //
    public function getConsumers($whitelist=false):array
    {
        $consumers['data']=array();
        $consumers['db']=array();
        $consumers['amqp']=array();

        if ($whitelist) // use white list of amqp consumers
        {
            $consumers['data']=$this->whitelist();
            $consumers['amqp']=$this->whitelist();

        } else { // get comsumers from consumerConfig

            foreach ($this->consumerConfig->getConsumers() as $consumer) {

                // amqp connections
                if($consumer->getConnection()==='amqp'){
                    $consumers['amqp'][]=$consumer->getName();
                }
                // db connections
                if($consumer->getConnection()==='db'){
                    $consumers['db'][]=$consumer->getName();
                }
                // all consumers
                $consumers['all'][]=$consumer->getName();
                $consumers['data'][$consumer->getName()]=array(
                    'connection' => $consumer->getConnection(),
                    'queue' => $consumer->getQueue()
                );
            }
        }

        return $consumers;
    }


    // manual list of amqp enabled consumers
    //
    public function whitelist(): array
    {
        return array(
            'gaiterjones_product_save',
            'gaiterjones_message_manager'
        );
    }

    // exclude these from env.php config
    //
    public function blacklist(): array
    {
        return array(
            'elgentos_magento_lcp_product_prewarm',
            'gaiterjones_product_save',
            'gaiterjones_message_manager',
            'async.operations.all'
        );
    }
}
