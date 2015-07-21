<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DbvInfoCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dbv:info')
            ->setDescription('Shows database versioning info')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require __DIR__ . '/../src/application/bootstrap.php';

        if (! defined('USE_DB_VERSIONING') || (defined('USE_DB_VERSIONING') && USE_DB_VERSIONING === false )) {
            $output->writeln('Database versioning not enabled. Check configuration file.');
            exit();
        }

        $db_versions_model = CodePax_DbVersions::factory();
        $latest_structure_version = $db_versions_model->getLatestVersion(CodePax_DbVersions::TYPE_CHANGE_SCRIPT);
        $latest_data_version = $db_versions_model->getLatestVersion(CodePax_DbVersions::TYPE_DATA_CHANGE_SCRIPT);
        $db_versioning_handler = CodePax_DbVersioning_Environments_Factory::factory(APPLICATION_ENVIRONMENT);
        $change_scripts = $db_versioning_handler->getChangeScripts();
        $data_change_scripts = $db_versioning_handler->getDataChangeScripts();

        $output->writeln('Database name: ' . DB_NAME);
        $output->writeln('Database structure version: ' . $latest_structure_version[CodePax_DbVersions::VERSION_ATTRIBUTE]);
        $output->writeln('Database structure last update: ' . $latest_structure_version[CodePax_DbVersions::DATE_ADDED_ATTRIBUTE]);
        $output->writeln('Database data version: ' . $latest_data_version[CodePax_DbVersions::VERSION_ATTRIBUTE]);
        $output->writeln('Database data last update: ' . (!empty($latest_data_version[CodePax_DbVersions::DATE_ADDED_ATTRIBUTE]) ? $latest_data_version[CodePax_DbVersions::DATE_ADDED_ATTRIBUTE] : 'n/a'));
        $output->writeln('');

        if (APPLICATION_ENVIRONMENT == 'dev') {
            $output->writeln('Baseline version: ' . $db_versioning_handler->getLatestBaselineVersion());
        }

        $output->writeln('Change structure scripts to run: ');
        $output->writeln(!empty($change_scripts) ? implode("\n", array_keys($change_scripts)) : 'n/a');

        $output->writeln('Change data scripts to run: ');
        $output->writeln(!empty($data_change_scripts) ? implode("\n", array_keys($data_change_scripts)) : 'n/a');
        
    }
}
