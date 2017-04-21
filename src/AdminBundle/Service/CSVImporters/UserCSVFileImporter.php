<?php

namespace AdminBundle\Service\CSVImporters;

use AdminBundle\Repository\UsersRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;

class UserCSVFileImporter extends AbstractCSVFileImporter
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var callback */
    public $importCallback;

    /** @var array */
    public $csvEntitySettersMap;

    /** @var UsersRepository $repository */
    public $repository;

    public function __construct(Registry $doctrine)
    {
        $this->entityManager = $doctrine->getManager();
    }

    public function processFile($csvFilePath)
    {
        $this->csvEntitySettersMap = [];
        $this->repository = $this->entityManager->getRepository('AdminBundle:Users');

        return parent::processFile($csvFilePath);
    }

    public function import(array $dataset)
    {
        if (empty($columnEntityMap)) {
            $this->csvEntitySettersMap = $this->csvColumnEntityMap($this->csvColumns);
        }

        $this->repository->importBulk($dataset, $this->csvEntitySettersMap);

        if (is_callable($this->importCallback)) {
            $importCallback = $this->importCallback;
            $importCallback($this);
        }
    }

    /**
     * @param array $csvColumnMap CSV column name array
     * @return array Entity name array
     */
    private function csvColumnEntityMap(array $csvColumnMap)
    {
        $name2EntityMap = [
            'First Name' => 'setFirstName',
            'Last Name' => 'setLastName',
            'Birthdate' => 'setBirthdayAsDotSeparatedDMY',
            'Email' => 'setEmail',
            'Home City' => 'setHomeCity',
            'Home Zip' => 'setHomeStreet',  // this was mixed in CSV
            'Home Address' => 'setHomeZip', // this was mixed in CSV
            'Phone' => 'setPhone',
            'Company Name' => 'setCompanyName',
            'Work City' => 'setCompanyCity',
            'Work Address' => 'setCompanyStreet',
            'Position' => 'setJobPosition',
            'CV' => 'setCv',
        ];

        $result = [];
        foreach ($csvColumnMap as $csvKey) {
            $result[] = isset($name2EntityMap[$csvKey]) ? $name2EntityMap[$csvKey] : "";
        }

        return $result;
    }
}
