<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbvCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dbv:run')
            ->setDescription('Run database versioning')
            ->addOption(
               'preserve-test-data',
               null,
               InputOption::VALUE_NONE,
               'Preseves test data'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require __DIR__ . '/../src/application/bootstrap.php';

        $preserve_test_data = false;

        if ($input->getOption('preserve-test-data')) {
            $preserve_test_data = true;
        }

        try {
            $db_versioning_handler = CodePax_DbVersioning_Environments_Factory::factory(APPLICATION_ENVIRONMENT);

            $generate_test_data = false;
            // generate test data
            if (APPLICATION_ENVIRONMENT == 'dev' && isset($preserve_test_data) && $preserve_test_data == true) {
                $generate_test_data = true;
            }

            $db_scripts_result = $db_versioning_handler->runScripts($generate_test_data);
            
            unset($db_scripts_result['run_change_scripts'], $db_scripts_result['run_data_change_scripts']);

        } catch (CodePax_DbVersioning_Exception $dbv_e) {
            $output->writeln($dbv_e->getMessage());
            exit();
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
            exit();
        }

    }
}
