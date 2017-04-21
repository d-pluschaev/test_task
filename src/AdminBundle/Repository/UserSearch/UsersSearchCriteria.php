<?php

namespace AdminBundle\Repository\UserSearch;

use AdminBundle\Repository\UsersRepository;
use Doctrine\ORM\QueryBuilder;

class UsersSearchCriteria
{
    /** @var UsersRepository */
    private $usersRepository;

    /** @var QueryBuilder */
    private $queryBuilder;

    public function __construct(UsersRepository $usersRepository, QueryBuilder $queryBuilder)
    {
        $this->usersRepository = $usersRepository;
        $this->queryBuilder = $queryBuilder;
    }

    public function avoidEmailDuplicates()
    {
        $qb2 = $this->usersRepository->createQueryBuilder('su');

        $this->queryBuilder->where($this->queryBuilder->expr()->in(
            'u.id',
            $qb2->select($qb2->expr()->min('su.id'))
                ->groupBy("su.email")
                ->getDQL()
        ));
    }

    public function processField($fieldName, $fieldValue)
    {
        $handlerName = "handle" . ucfirst($fieldName);
        $this->$handlerName($fieldValue);
    }

    private function handleLastName($value)
    {
        $this->handleFieldEqual("lastName", $value);
    }

    private function handleFirstName($value)
    {
        $this->handleFieldEqual("firstName", $value);
    }

    private function handleBirthday($value)
    {
        $this->handleFieldEqual("birthday", new \DateTime($value));
    }

    private function handleAge($value)
    {
        $dFirst   = new \DateTime();
        $dFirst->modify("-$value year");
        $dFirst->modify("first day of January");
        $dFirst->setTime(0, 0, 0);
        $dSecond = clone $dFirst;
        $dSecond->modify("last day of December");
        $dSecond->setTime(23, 59, 59);

        $this->queryBuilder->andWhere("u.birthday BETWEEN :d1 AND :d2")
            ->setParameter("d1", $dFirst)
            ->setParameter("d2", $dSecond);
    }

    private function handleEmail($value)
    {
        $this->handleFieldEqual("email", $value);
    }

    private function handleHomeAddress($value)
    {
        $addressParts = $this->parseFullAddress($value);
        foreach($addressParts as $addressType => $addressPart) {
            $this->handleFieldEqual("home" . ucfirst($addressType), $addressPart);
        }
    }

    private function handleCompanyAddress($value)
    {
        $addressParts = $this->parseFullAddress($value);
        unset($addressParts['zip']); // we don't have zip for company
        foreach($addressParts as $addressType => $addressPart) {
            $this->handleFieldEqual("company" . ucfirst($addressType), $addressPart);
        }
    }

    private function handlePhone($value)
    {
        $value = preg_replace("~[\D]~", "", $value);
        $this->handleFieldEqual("phone", $value);
    }

    private function handleCompanyName($value)
    {
        $this->handleFieldEqual("companyName", $value);
    }

    private function handleJobPosition($value)
    {
        $this->handleFieldEqual("jobPosition", $value);
    }

    private function handleCV($value)
    {
        $this->handleFieldLike("cv", $value);
    }

    private function parseFullAddress($value)
    {
        // 1. "city, zip address";
        // 2. "zip address";
        // 3. "city" or "zip" or "address".
        $addressParts = [];

        $ppCity = "[\D]{2,}";
        $ppZip = "[\d]{2,5}";
        $ppStreet = "[\D]+ [\d]+[^\d]*";

        if (preg_match("~($ppCity), ($ppZip) ($ppStreet)~", $value, $matches)) {
            $addressParts['city'] = $matches[1];
            $addressParts['zip'] = $matches[2];
            $addressParts['street'] = $matches[3];
        } else if (preg_match("~($ppZip) ($ppStreet)~", $value, $matches)) {
            $addressParts['zip'] = $matches[1];
            $addressParts['street'] = $matches[2];
        } elseif (preg_match("~($ppStreet)~", $value, $matches)) {
            $addressParts['street'] = $matches[1];
        } elseif (preg_match("~($ppZip)~", $value, $matches)) {
            $addressParts['zip'] = $matches[1];
        } elseif (preg_match("~($ppCity)~", $value, $matches)) {
            $addressParts['city'] = $matches[1];
        }

        return array_map("trim", $addressParts);
    }

    private function handleFieldEqual($field, $value)
    {
        $this->queryBuilder->andWhere("u.$field=:$field")->setParameter($field, $value);
    }

    private function handleFieldLike($field, $value)
    {
        $this->queryBuilder->andWhere("u.$field LIKE :$field")->setParameter($field, "%$value%");
    }
}
