<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbvInstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dbv:install')
            ->setDescription('Creates versioning table')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = CodePax_Config::getInstance(CONFIG_PATH . 'config.ini');
        
        try {
            $sql_engine = CodePax_DbVersioning_SqlEngines_Factory::factory($configuration );
            $dir = ABS_PATH . 'app' . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR;

            switch (strtolower($configuration->db_engine)) {
                case 'mysql':
                    $sql_file_name = 'db_versions_mysql.sql';
                    break;
                case 'pgsql':
                    $sql_file_name = 'db_versions_pgsql.sql';
                    break;
                case 'sqlsrv':
                    $sql_file_name = 'db_versions_sqlsrv.sql';
                    break;
            }
            $sql_engine->executeChangeScript($dir . $sql_file_name);
        } catch (CodePax_DbVersioning_Exception $e) {
            $output->writeln('An error ocurred: ' . $e->getMessage());
            exit();
        } catch (Exception $e) {
            $output->writeln('Generic error: ' . $e->getMessage());
            exit();
        }

        $output->writeln('CodePax successfully installed!');        
    }
}
