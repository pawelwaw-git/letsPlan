<?php

namespace App\Repository;

use App\Entity\TaskCalendar;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaskCalendar>
 *
 * @method TaskCalendar|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskCalendar|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskCalendar[]    findAll()
 * @method TaskCalendar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskCalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskCalendar::class);
    }

    public function save(TaskCalendar $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TaskCalendar $entity, bool $flush = false): void
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
     * @return TaskCalendar[] Returns an array of TaskCalendar objects
     */
    public function getTodaysFinishedTasksWithGoals(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.Date = :today')
            ->andWhere('t.isDone = :isDone')
            ->setParameter('today', new \DateTime('today'))
            ->setParameter('isDone', true)
            ->leftJoin('t.Goal', 'g')
            ->addSelect('g')
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, string>
     */
    public function getTodaysUnfinishedTasksWithGoals(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.Date = :today')
            ->andWhere('t.isDone = :isDone')
            ->setParameter('today', new \DateTime('today'))
            ->setParameter('isDone', false)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, string>
     */
    public function getStatsForPreviousTasks(DateTime $lastDay): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.Date >= :lastDay')
            ->andWhere('t.Date <= :now')
            ->setParameter('lastDay', $lastDay)
            ->setParameter('now', new \DateTime('now'))
            ->addGroupBy('t.Date')
            ->addGroupBy('t.isDone')
            ->orderBy('t.Date', 'ASC')
            ->select('count(t.id) as Quantity', 't.Date', 't.isDone')
            ->getQuery()
            ->getResult();
    }

    public function getQuantityOfTasksTypes(string $period): int
    {
        return $this->createQueryBuilder('t')
            ->andWhere('g.Repeatable = :period')
            ->setParameter('period', $period)
            ->leftJoin('t.Goal', 'g')
            ->select('count(t.id) as Quantity')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws Exception
     */
    public function truncate(): void
    {
        $connection = $this->getEntityManager()->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeQuery($platform->getTruncateTableSQL('my_table', false));
    }
}
