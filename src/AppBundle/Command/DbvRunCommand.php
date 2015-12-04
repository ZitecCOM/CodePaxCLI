<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbvRunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dbv:run')
            ->setDescription('Runs database versioning')
            ->addOption(
                'preserve-test-data',
                null,
                InputOption::VALUE_NONE,
                'Preseves test data'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = CodePax_Config::getInstance(CONFIG_PATH . 'config.ini');
        $preserve_test_data = false;

        if ($input->getOption('preserve-test-data')) {
            $preserve_test_data = true;
        }

        try {
            $db_versioning_handler = CodePax_DbVersioning_Environments_Factory::factory($configuration);

            $generate_test_data = false;
            // generate test data
            if (strtolower($configuration->application_environment) == 'dev' && isset($preserve_test_data) && $preserve_test_data == true) {
                $generate_test_data = true;
            }

            $db_scripts_result = $db_versioning_handler->runScripts($generate_test_data);

            unset($db_scripts_result['run_change_scripts'], $db_scripts_result['run_data_change_scripts']);

        } catch (CodePax_DbVersioning_Exception $e) {
            $output->writeln($e->getMessage());
            exit();
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
            exit();
        }

        $output->writeln('Everything went smoothly!');
    }
}
