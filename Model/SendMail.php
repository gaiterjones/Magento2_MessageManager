<?php

declare(strict_types=1);

namespace Gaiterjones\MessageManager\Model;

use Exception;
use Magento\Framework\App\Area;
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
    public function sendMailToCustomer($sendTo, $customerId, $emailTemplate, $storeId, $sender)
    {

        try {

            if (!is_array($sender))
            {
                $sender=array(
                    'email' => $this->getStoreEmail(),
                    'name' => $this->getStorename()
                );
            }

            /** @var Customer $mergedCustomerData */
            $customer=$this->getCustomerById($customerId);
            $customerEmailData = $this->customerRegistry->retrieve($customer->getId());
            $customerEmailData->setData('name', $customerEmailData->getName());

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions([
                    'area' => Area::AREA_FRONTEND,
                    'store' => $storeId,
                ])
                ->setTemplateVars([
                    'customer' => $customerEmailData
                ])
                ->setFrom($sender)
                ->addTo($sendTo)
                ->getTransport();
            $transport->sendMessage();

            return true;

        } catch (Exception $e) {

            $this->logger->critical($e->getMessage());
            $this->logger->info('sendto:'.$sendTo.' customerId:'.$customerId.' emailTemplate:'.$emailTemplate.' storeId:'.$storeId.' sender:'.print_r($sender,true). ' customer:'. $customerEmailData->getName());
        }

        return false;
    }

    /**
     * @param $customerId
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomerById($customerId)
    {
        $customerModel = $this->customerRegistry->retrieve($customerId);

        return $customerModel->getDataModel();
    }

    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getStorename()
    {
        return $this->_scopeConfig->getValue(
            'trans_email/ident_sales/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
