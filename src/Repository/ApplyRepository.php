<?php

namespace App\Repository;

use App\Entity\Apply;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Apply|null find($id, $lockMode = null, $lockVersion = null)
 * @method Apply|null findOneBy(array $criteria, array $orderBy = null)
 * @method Apply[]    findAll()
 * @method Apply[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Apply::class);
    }
}
