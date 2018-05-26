<?php

namespace App\Repository;

use App\Entity\Apply;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findAvailable()
    {
        return $this->createQueryBuilder('p')
            ->leftJoin(Apply::class, 'a', 'WITH', 'p.id = a.project AND a.status = \'accepted\'')
            ->andWhere('p.registerDeadline >= :date')
            ->setParameter('date', date('Y-m-d'))
            ->andHaving('count(a.id) < p.crewCount')
            ->groupBy('p.id')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findApplied($worker)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin(Apply::class, 'a', 'WITH', 'p.id = a.project')
            ->where('a.worker = :worker')
            ->setParameter('worker', $worker)
            ->orderBy('a.id','DESC')
            ->getQuery()
            ->getResult()
        ;
    }

//    /**
//     * @return Project[] Returns an array of Project objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
