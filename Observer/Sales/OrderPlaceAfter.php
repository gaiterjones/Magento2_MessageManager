<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Gaiterjones\MessageManager\Observer\Sales;
use Gaiterjones\MessageManager\Helper\Data as Helper;

class OrderPlaceAfter implements \Magento\Framework\Event\ObserverInterface
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

        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getIncrementId();
        $topicName='gaiterjones.magento.message.manager';
        
        $this->helper->publisher->execute($topicName,array(
            'action' => 'newordernotification',
            'data' => array(
                'message' => 'Magento2 new order received #'.$orderId,
            )
        ));
    }
}

