<?php

namespace AppBundle\Repository;

/**
 * ClientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientRepository extends \Doctrine\ORM\EntityRepository
{
    public function searchByName($name)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id, c.name')
            ->where('c.name LIKE :name')
            ->setParameter('name', '%'.$name.'%');

        return $qb->getQuery()->getResult();
    }
}
