<?php

namespace AdminBundle\Command;

use AdminBundle\Service\CSVImporters\AbstractCSVFileImporter;
use AdminBundle\Service\CSVImporters\UserCSVFileImporter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCsvCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('admin:users-import-csv')
            ->setDescription('Imports users from CSV file')
            ->setHelp(
                "Imports users from CSV file. CSV must have following columns:\n"
                . "First Name	Last Name	Birthdate   Age Email	Home City	Home Zip	Home Address	Phone	\n"
                . "Company Name	Work City	Work Address	Position	CV"
            )
            ->addArgument('file_path', InputArgument::REQUIRED, 'path to a CSV file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln('Opening file for reading...');
        $progressBar = $io->createProgressBar();

        $csvFilePath = $input->getArgument('file_path');

        /** @var UserCSVFileImporter $csvFileImporter */
        $csvFileImporter = $this->getContainer()->get('admin.csv_file_importer.users');

        // callback
        $csvFileImporter->importCallback = function (AbstractCSVFileImporter $sender) use ($progressBar) {
                // update progress
                if ($progressBar->getMaxSteps() == 0) {
                    $progressBar->start($sender->progressMax);
                }
                $progressBar->setProgress($sender->progressStep);
            };

        $io->writeln("Importing using threshold: {$csvFileImporter->importThreshold}...");

        $lineNumbers = $csvFileImporter->processFile($csvFilePath);

        $io->newLine();
        $io->success("All operations done, $lineNumbers lines imported. Took: "
            . round(time() - $progressBar->getStartTime()) . " seconds");
        $progressBar->finish();
    }
}
