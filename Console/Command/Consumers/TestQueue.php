<?php

declare(strict_types=1);

namespace Gaiterjones\MessageManager\Console\Command\Consumers;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State as AppState;
use Gaiterjones\MessageManager\Helper\Data as Helper;

/**
 * MessageManager TestQueue
 */
class TestQueue extends Command
{
    private const OPTION_TOPIC = 'topic';

    private $helper;

    private $_appState;

    public function __construct(
        AppState $appState,
        Helper $helper
    ) {
        $this->_appState = $appState;
        $this->helper=$helper;
        parent::__construct();
    }

    /**
     * Initialization of the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('messagemanager:testqueue')
            ->setDescription('Get consumer config for Message Manager')
            ->addOption(
                self::OPTION_TOPIC,
                null,
                InputOption::VALUE_OPTIONAL,
                'Test queue with topic'
            );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $topicName='gaiterjones.magento.message.manager';
        $topic = (string)$input->getOption(self::OPTION_TOPIC);
        if ($topic){$topicName=$input->getOption(self::OPTION_TOPIC);}

        $message = 'queue:topic '. $topicName. ' testing...';

        // SIMPLE TEST MESSAGE
        $this->helper->publisher->execute($topicName,array(
            'action' => 'test',
            'data' => array('message' => $message)
        ));

        // !! RESTART CONSUMER AFTER MAKING ANY CHANGES 
        //

        // EMAIL EXAMPLE
        // to CUSTOM EMAIL address i.e. admin
        // email template = id of email template
        // customerid = id of customer
        /*
        $this->helper->publisher->execute($topicName,array(
            'action' => 'sendmailtocustomer',
            'data' => array(
                'emailtemplate' => '3',
                'storeid' => 0,
                'sender' => array(
                    'email' => 'paj@gaiterjones.com',
                    'name' => 'TEST'
                )
                'sendto' => 'paj@gaiterjones.com',
                'customerid' => '48'
            )
        ));

        // to CUSTOMER EMAIL address from customer account
        $this->helper->publisher->execute($topicName,array(
            'action' => 'sendmailtocustomer',
            'data' => array(
                'emailtemplate' => '3',
                'storeid' => 0,
                'sender' => 0,
                'sendto' => 0,
                'customerid' => '48'
            )
        ));
        */

        $output->writeln(sprintf($message));
        return 0;
    }

}
