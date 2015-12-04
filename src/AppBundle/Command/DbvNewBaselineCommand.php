<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbvNewBaselineCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dbv:newbaseline')
            ->setDescription('Creates a new baseline')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = CodePax_Config::getInstance(CONFIG_PATH . 'config.ini');

        if (strtolower($configuration->application_environment) == 'stg') {
            try {
                $db_versioning_handler = CodePax_DbVersioning_Environments_Factory::factory($configuration);
                $db_versioning_handler->generateBaseline();
            } catch (CodePax_DbVersioning_Exception $e) {
                $output->writeln('An error ocurred: ' . $e->getMessage());
                exit();
            } catch (Exception $e) {
                $output->writeln('Generic error: ' . $e->getMessage());
                exit();
            }
            $output->writeln('A new baseline has been generated!');
        } else {
            $output->writeln('You are not running on stg. The ' . strtolower($configuration->application_environment) . ' environment was detected');
        }
        
    }
}
