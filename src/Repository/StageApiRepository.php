<?php

namespace App\Repository;

use App\Entity\StageApi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method StageApi|null find($id, $lockMode = null, $lockVersion = null)
 * @method StageApi|null findOneBy(array $criteria, array $orderBy = null)
 * @method StageApi[]    findAll()
 * @method StageApi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StageApiRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, StageApi::class);
    }

    // /**
    //  * @return StageApi[] Returns an array of StageApi objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StageApi
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
