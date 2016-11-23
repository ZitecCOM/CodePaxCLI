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
                'Preserves test data'
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

            // run the scripts
            $db_scripts_result = $db_versioning_handler->runScripts($generate_test_data);

            //print the results
            if (isset($db_scripts_result['change_scripts'])) {
                $this->outputResults($output, 'Change scripts results:', $db_scripts_result['change_scripts']);
            }

            if (isset($db_scripts_result['data_change_scripts'])) {
                $this->outputResults($output, 'Data change scripts results:', $db_scripts_result['data_change_scripts']);
            }
            $output->writeln('Done!');

        } catch (CodePax_DbVersioning_Exception $e) {
            $output->writeln($e->getMessage());
            exit();
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
            exit();
        }
    }


    /**
     * Format the output and print it
     *
     * @param $output
     * @param $title
     * @param $results
     */
    protected function outputResults($output, $title, $results)
    {
        // print the title
        $output->writeln($title);

        // print the results
        foreach ($results as $script_name => $script_output) {
            // format the output
            if ($script_output == 'ok') {
                $formatted_script_output = sprintf("<info>%s</info>", $script_output);
            } else {
                $formatted_script_output = sprintf("<fg=red>%s</>", $script_output);
            }

            // print the message
            $output->writeln(sprintf('%s: %s', $script_name, $formatted_script_output));
        }

        // print separator line
        $output->writeln('-------');
    }
}
