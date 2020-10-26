<?php

namespace App\Repository;

use App\Entity\Tricks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tricks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tricks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tricks[]    findAll()
 * @method Tricks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TricksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tricks::class);
    }

    /**
    * @return Tricks[] Returns an array of Tricks objects
    */
    public function findFirstTricks($limit)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT tricks
             FROM App\Entity\Tricks tricks
             ORDER BY tricks.dateAtCreated DESC' 
        )->setMaxResults($limit);
        
        return $query->getResult();

    }

    /**
    * @return Tricks[] Returns an array of Tricks objects
    */
    public function findMoreTricks($offset)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT tricks
             FROM App\Entity\Tricks tricks
             ORDER BY tricks.dateAtCreated DESC' 
        )->setFirstResult($offset);
        
        return $query->getResult();
        
    }

}
