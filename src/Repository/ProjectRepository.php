<?php

namespace App\Repository;

use App\Entity\Apply;
use App\Entity\Project;
use App\Entity\Worker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findByFilters($filters)
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin(Apply::class, 'a', 'WITH', 'p.id = a.project AND a.status = \'accepted\'')
            ->andWhere('p.registerDeadline >= :date')
            ->setParameter('date', date('Y-m-d'))
            ->andHaving('count(a.id) < p.crewCount')
            ->groupBy('p.id')
        ;
        foreach($filters as $filter) {
            if(method_exists($this,$filter['method'])) {
                $this->{$filter['method']}($qb, $filter['value']);
            }
        }
        return $qb->getQuery()->getResult();
    }

    private function filterBudgetFrom(QueryBuilder $qb, $budget)
    {
        return $qb->andWhere('p.budget >= :budget')
            ->setParameter('budget', $budget);
    }

    private function filterBudgetTo(QueryBuilder $qb, $budget)
    {
        return $qb->andWhere('p.budget <= :budget')
            ->setParameter('budget', $budget);
    }

    private function filterSkills(QueryBuilder $qb, $skills)
    {
        $alias = uniqid('s');
        return $qb->leftjoin('p.skills',$alias,'WITH', $alias.' NOT IN (:skills)')
            ->setParameter('skills', $skills)
            ->andHaving('count('.$alias.') = 0');
    }
}
