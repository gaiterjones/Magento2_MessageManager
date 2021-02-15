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
 * MessageManager GetConsumers
 */
class GetConsumers extends Command
{
    private const OPTION_JSON = 'json';
    private const OPTION_WHITELIST = 'whitelist';

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
        $this->setName('messagemanager:getconsumers')
            ->setDescription('Get consumer list for Message Manager')
            ->addOption(
                self::OPTION_JSON,
                null,
                InputOption::VALUE_NONE,
                'Output consumer data in json format for consumer container.'
            )
            ->addOption(
                self::OPTION_WHITELIST,
                null,
                InputOption::VALUE_NONE,
                'Get whitelisted consumer config'
            );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $json = (bool)$input->getOption(self::OPTION_JSON);
        $whitelist = (bool)$input->getOption(self::OPTION_WHITELIST);

        $consumers=$this->helper->getConsumers($whitelist);

        // default output
        //
        $message = print_r($consumers['data'],true);

        // json output
        //
        if ($json)
        {$message = json_encode($consumers['amqp']);}

        $output->writeln(sprintf($message));
        return 0;
    }

}
