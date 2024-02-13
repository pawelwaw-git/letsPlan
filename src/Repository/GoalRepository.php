<?php

namespace App\Repository;

use App\Entity\Goal;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Goal>
 *
 * @method Goal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Goal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Goal[]    findAll()
 * @method Goal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Goal::class);
    }

    public function save(Goal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Goal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return Goal[] Returns an array of Goal objects
     */
    public function findGoalsToSchedule(DateTime $lastScheduleDate): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.Active = :isActive')
            ->andWhere('g.LastDateSchedule is null or g.LastDateSchedule < :lastScheduleDate')
            ->setParameter('isActive', true)
            ->setParameter('lastScheduleDate', $lastScheduleDate)
            ->orderBy('g.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
