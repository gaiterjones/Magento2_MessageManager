<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gaiterjones\MessageManager\Observer\Customer;
use Gaiterjones\MessageManager\Helper\Data as Helper;

class RegisterSuccess implements \Magento\Framework\Event\ObserverInterface
{
    private $helper;

    public function __construct(
        Helper $helper
    ) {
        $this->helper=$helper;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        
        $topicName='gaiterjones.magento.message.manager';
        
        $customer = $observer->getEvent()->getData('customer');
        
        $email = $customer->getEmail();
        $first_name = $customer->getFirstname();
        $last_name = $customer->getLastname();
        
        $this->helper->publisher->execute($topicName,array(
            'action' => 'newcustomernotification',
            'data' => array(
                'message' => 'Magento2 new customer registration : '. $first_name.' '.$last_name,
            )
        ));
    }
}

