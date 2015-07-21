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
        require __DIR__ . '/../src/application/bootstrap.php';

        if (! defined('USE_DB_VERSIONING') || (defined('USE_DB_VERSIONING') && USE_DB_VERSIONING === false )) {
            $output->writeln('Database versioning not enabled. Check configuration file.');
            exit();
        }

        if (APPLICATION_ENVIRONMENT == 'stg') {
            try {
                $db_versioning_handler = CodePax_DbVersioning_Environments_Factory::factory(APPLICATION_ENVIRONMENT);
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
            $output->writeln('You are not running on stg. The ' . APPLICATION_ENVIRONMENT . ' environment was detected');
        }
        
    }
}
