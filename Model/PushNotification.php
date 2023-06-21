<?php

declare(strict_types=1);

namespace Gaiterjones\MessageManager\Model;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class PushNotification
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     *
     * @return bool
     */
    public function message($message)
    {

        $token=$this->getConfig('messagemanager/pushover/api_token');
        $user=$this->getConfig('bmessagemanager/pushover/api_user');
        
        try {

            curl_setopt_array($ch = curl_init(), array(
                CURLOPT_URL => "https://api.pushover.net/1/messages.json",
                CURLOPT_POSTFIELDS => array(
                    "token" => $token,
                    "user" => $user,
                    "message" => $message
                ),
                CURLOPT_SAFE_UPLOAD => true,
                CURLOPT_RETURNTRANSFER => true,
            ));

            $response = curl_exec($ch);
            curl_close($ch);

            return true;

        } catch (Exception $e) {

            $this->logger->critical($e->getMessage());
        }

        return false;

    }

    private function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
