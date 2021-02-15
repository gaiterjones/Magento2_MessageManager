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
 * MessageManager GetConfig
 */
class GetConfig extends Command
{
    private const OPTION_WHITELIST = 'whitelist';
    private const OPTION_BUILDCONFIG = 'buildconfig';
    private const OPTION_SAVECONFIG = 'saveconfig';

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
        $this->setName('messagemanager:getconfig')
            ->setDescription('Get consumer config for Message Manager')
            ->addOption(
                self::OPTION_BUILDCONFIG,
                null,
                InputOption::VALUE_NONE,
                'Build consumer env queue config for all DB queues'
            )
            ->addOption(
                self::OPTION_SAVECONFIG,
                null,
                InputOption::VALUE_NONE,
                'Save consumer queue config to ENV'
            )
            ->addOption(
                self::OPTION_WHITELIST,
                null,
                InputOption::VALUE_NONE,
                'Build consumer env queue config for WHITELISTED queues'
            );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $whitelist = (bool)$input->getOption(self::OPTION_WHITELIST);
        $buildconfig = (bool)$input->getOption(self::OPTION_BUILDCONFIG);
        $saveconfig = (bool)$input->getOption(self::OPTION_SAVECONFIG);

        $consumers=$this->helper->getConsumers($whitelist);

        // build config output
        //
        if ($buildconfig)
        {
            if ($whitelist)
            {
                if (count($consumers['db']) > 0)
                {
                $message='The following WHITELISTED consumers will be CONFIGURED for amqp...'. PHP_EOL.
                    print_r($this->helper->config->buildconfig($this->helper->whitelist(),$this->helper->blacklist(),$saveconfig),true);
                } else {
                    $message='ERROR: All queues already configured for AMQP...';
                }
            } else {
                if (count($consumers['db']) > 0)
                {
                $message='The following DB consumers will be CONFIGURED for amqp...'. PHP_EOL.
                    print_r($this->helper->config->buildconfig($consumers['db'],$this->helper->blacklist(),$saveconfig),true);
                } else {
                    $message='ERROR: All queues already configured for AMQP...';
                }
            }
        } else {

            // default output
            //
            $message = 'ENV amqp queue config: '.print_r($this->helper->config->getconfig(),true);
        }

        $output->writeln(sprintf($message));
        return 0;
    }

}
