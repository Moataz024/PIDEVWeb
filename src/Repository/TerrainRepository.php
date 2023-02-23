<?php

namespace App\Repository;

use App\Entity\Terrain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Terrain>
 *
 * @method Terrain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Terrain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Terrain[]    findAll()
 * @method Terrain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TerrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terrain::class);
    }

    public function save(Terrain $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Terrain $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findRecentTerrains(int $limit = 3): array
    {
        return $this->findBy([], ['updatedAt' => 'DESC'], $limit);
    }
    public function findByFilters(?string $location, ?string $sportType, ?float $rentPrice): array
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.updatedAt', 'DESC');
    
        if ($location) {
            $qb->andWhere('t.city = :location OR t.country = :location ')
               ->setParameter('location', $location);
        }
    
        if ($sportType) {
            $qb->andWhere('t.sportType = :sportType')
               ->setParameter('sportType', $sportType);
        }
    
        if ($rentPrice) {
            $qb->andWhere('t.rentPrice <= :rentPrice')
               ->setParameter('rentPrice', $rentPrice);
        }
    
        return $qb->getQuery()->getResult();
    }
    
    /*public function findByOwner($userId)
    {
        $qd= $this->createQueryBuilder('t');
        $qd->where('t.owner_id = :userId')->setParameter('userId', $userId);
        return $qd->getQuery()->getResult();
    }*/

//    /**
//     * @return Terrain[] Returns an array of Terrain objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Terrain
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
