<?php

namespace AppBundle\Repository;

class UserRepository extends \Doctrine\ORM\EntityRepository {
    public function searchByname($name)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u.id, u.firstName, u.lastName')
            ->where('u.firstName LIKE :name')
            ->orWhere('u.lastName LIKE :name')
            ->setParameter('name', '%'.$name.'%');

        return $qb->getQuery()->getResult();
    }
}
