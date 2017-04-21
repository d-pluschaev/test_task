<?php

namespace AdminBundle\Repository;

use AdminBundle\Repository\UserSearch\UsersSearchCriteria;
use Doctrine\ORM\Internal\Hydration\IterableResult;

/**
 * UsersRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UsersRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param array $rows
     * @param array $setterMap
     */
    public function importBulk(array $rows, array $setterMap)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null); // disable to save memory
        $entityMetadata = $this->getClassMetadata();

        $userEntityTpl = $entityMetadata->newInstance();

        foreach ($rows as $row) {
            /** @var \AdminBundle\Entity\Users $userEntity */
            $userEntity = clone $userEntityTpl;

            foreach ($row as $fieldIndex => $fieldValue) {
                $setterName = $setterMap[$fieldIndex];
                $userEntity->$setterName($fieldValue);
            }

            $em->persist($userEntity);
        }
        $em->flush();
        $em->clear();
    }

    /**
     * @param array $fields
     * @return array
     */
    public function processSearch(array $fields, $offset)
    {
        $limit = 10;

        $qb = $this->createQueryBuilder('u');

        $sc = new UsersSearchCriteria($this, $qb);

        $sc->avoidEmailDuplicates();

        foreach($fields as $fieldName => $fieldValue) {
            $sc->processField($fieldName, $fieldValue);
        }

        // count
        $cqb = clone $qb;
        $cqb->select('COUNT(u)');
        $count = $cqb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return [
            'count' => $count,
            'offset' => $offset,
            'limit' => $limit,
            'terms' => $fields,
            'results' => $this->prepareResults($qb->getQuery()->getArrayResult())
        ];
    }

    /**
     * @param array $results
     * @return array
     */
    private function prepareResults(array $results)
    {
        foreach($results as &$row) {
            $row['age'] = "";
            if ($row['birthday'] instanceof \DateTime) {
                $row['age'] = (new \DateTime())->format("Y") - $row['birthday']->format("Y");
                $row['birthday'] = $row['birthday']->format("d.m.Y");
            }
            $row['homeAddress'] = "{$row['homeCity']}, {$row['homeZip']} {$row['homeStreet']}";
            $row['companyAddress'] = "{$row['companyCity']}, {$row['companyStreet']}";
        }
        return $results;
    }
}