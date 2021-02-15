<?php

declare(strict_types=1);

namespace Gaiterjones\MessageManager\Model\MessageQueues;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentConfigWriter;
use Magento\Framework\Config\File\ConfigFilePool;

/**
 * MessageManager Config
 */
class Config
{
    private $deploymentConfig;

    private $deploymentConfigWriter;

    public function __construct(
        DeploymentConfig $deploymentConfig,
        DeploymentConfigWriter $deploymentConfigWriter
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->deploymentConfigWriter = $deploymentConfigWriter;
    }

    public function buildconfig
    (
        $consumers,
        $blacklist,
        $save=false
    ): array
    {
        $buildconfig=array();
        $coreconfig=$this->getconfig();

        $buildconfig['amqp']=$coreconfig['amqp'];
        $buildconfig['consumers_wait_for_messages']=$coreconfig['consumers_wait_for_messages'];

        foreach ($consumers as $consumer)
        {
            if (in_array($consumer, $blacklist)) {continue;}

            $topic[$consumer] = ['publisher' => 'amqp-magento'];

            $config['publishers'][$consumer]= [
                        'connections' => [
                            'amqp' => [
                                'name' => 'amqp',
                                'exchange' => 'magento',
                                'disabled' => false
                            ],
                            'db' => [
                                'name' => 'db',
                                'disabled' => true
                            ]
                        ]
                ];

            $consumerconfig[$consumer] = ['connection' => 'amqp'];
        }
        
        $buildconfig['topics']=$topic;
        $buildconfig['config']=$config;
        $buildconfig['consumers']=$consumerconfig;

        if ($save)
        {$this->setconfig($buildconfig);}

        return $buildconfig;
    }

    public function getconfig(): array
    {
        return $this->deploymentConfig->get("queue");
    }

    public function setconfig($config)
    {
        return $this->deploymentConfigWriter->saveConfig([ConfigFilePool::APP_ENV => ['queue' => $config]]);
    }
}
