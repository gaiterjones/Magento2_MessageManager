<?php

declare(strict_types=1);

namespace Gaiterjones\MessageManager\Model;

use Exception;
use Magento\Framework\App\Area;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class SendMail
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    public function __construct(
        LoggerInterface $logger,
        CustomerRegistry $customerRegistry,
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $sendTo
     * @param $customerId
     * @param $emailTemplate
     * @param $storeId
     * @param $sender
     *
     * @return bool
     */
    public function sendMailToCustomer($sendTo=false, $customerId=false, $emailTemplate, $storeId, $sender)
    {

        try {

            if (!is_array($sender))
            {
                $sender=array(
                    'email' => $this->getStoreEmail(),
                    'name' => $this->getStorename()
                );
            }

            $customer=$this->getCustomerById($customerId);
            $toName='Unknown';

            if ($customer instanceof Customer)
            {
                $toName=$customer->getName();
                $customer->setData('name', $toName);

                if (!$sendTo)
                {
                    $sendTo=$customer->getEmail();
                }

                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($emailTemplate)
                    ->setTemplateOptions([
                        'area' => Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ])
                    ->setTemplateVars([
                        'customer' => $customer
                    ])
                    ->setFrom($sender)
                    ->addTo($sendTo)
                    ->getTransport();

            } else {

                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($emailTemplate)
                    ->setTemplateOptions([
                        'area' => Area::AREA_FRONTEND,
                        'store' => $storeId,
                    ])
                    ->setFrom($sender)
                    ->addTo($sendTo)
                    ->getTransport();
            }


            $transport->sendMessage();

            return true;

        } catch (Exception $e) {

            $this->logger->critical($e->getMessage());
            $this->logger->info('sendto:'.$sendTo.' customerId:'.$customerId.' emailTemplate:'.$emailTemplate.' storeId:'.$storeId.' sender:'.print_r($sender,true). ' name:'. $toName);
        }

        return false;
    }

    public function getCustomerById($customerId)
    {
        return $this->customerRegistry->retrieve($customerId);
    }

    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getStorename()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_general/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
