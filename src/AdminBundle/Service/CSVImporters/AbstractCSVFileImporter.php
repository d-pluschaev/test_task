<?php

namespace AdminBundle\Service\CSVImporters;

abstract class AbstractCSVFileImporter
{
    /** @var int Depends on memory available */
    public $importThreshold = 1000;

    /** @var string */
    public $csvDelimiter = ";";

    /** @var array */
    public $csvColumns = [];

    /** @var int */
    public $progressMax = 0;

    /** @var int */
    public $progressStep = 0;

    /**
     * @param $csvFilePath
     * @throws \Exception
     */
    public function processFile($csvFilePath)
    {
        if (!is_file($csvFilePath)) {
            throw new \Exception("File does not exist: " . $csvFilePath);
        }

        try {
            $csvHandle = fopen($csvFilePath, 'r');
            $this->progressMax = filesize($csvFilePath);

            $lineNumber = 0;
            $bulkDataset = [];

            while (!feof($csvHandle)) {
                $line = fgetcsv($csvHandle, 0, $this->csvDelimiter);

                // fill column names
                if ($lineNumber == 0) {
                    $this->csvColumns = $line;
                    $lineNumber++;
                } else if ($line) {
                    $bulkDataset[] = $line;

                    if ($lineNumber % $this->importThreshold == 0) {
                        $this->import($bulkDataset);
                        $bulkDataset = [];
                        $this->progressStep = ftell($csvHandle);
                    }
                    $lineNumber++;
                }
            }
            $this->import($bulkDataset);
        } finally {
            fclose($csvHandle);
        }

        return $lineNumber > 0 ? $lineNumber - 1 : 0;
    }

    abstract public function import(array $dataset);
}
