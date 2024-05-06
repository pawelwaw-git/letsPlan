<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TaskCalendar;
use Carbon\Carbon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder as QueryBuilderAlias;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @extends ServiceEntityRepository<TaskCalendar>
 *
 * @method null|TaskCalendar find($id, $lockMode = null, $lockVersion = null)
 * @method null|TaskCalendar findOneBy(array $criteria, array $orderBy = null)
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
            ->setParameter('today', Carbon::now())
            ->setParameter('isDone', true)
            ->leftJoin('t.Goal', 'g')
            ->addSelect('g')
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<int, TaskCalendar[]>
     */
    public function getTodaysUnfinishedTasksWithGoals(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.Date = :today')
            ->andWhere('t.isDone = :isDone')
            ->setParameter('today', Carbon::now())
            ->setParameter('isDone', false)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return array<int, mixed>
     */
    public function getStatsForPreviousTasks(\DateTime $lastDay): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.Date >= :lastDay')
            ->andWhere('t.Date <= :now')
            ->setParameter('lastDay', $lastDay)
            ->setParameter('now', Carbon::now())
            ->addGroupBy('t.Date')
            ->addGroupBy('t.isDone')
            ->orderBy('t.Date', 'ASC')
            ->select('count(t.id) as Quantity', 't.Date', 't.isDone')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getQuantityOfTasksTypes(string $period): int
    {
        return $this->createQueryBuilder('t')
            ->andWhere('g.Repeatable = :period')
            ->setParameter('period', $period)
            ->leftJoin('t.Goal', 'g')
            ->select('count(t.id) as Quantity')
            ->getQuery()
            ->getSingleScalarResult()
        ;
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

    /**
     * @return Pagerfanta<TaskCalendar>
     *
     * @throws QueryException
     * @throws InvalidOperatorException
     * @throws InvalidFilterException
     */
    public function getPaginatedWithFilterAndSort(int $page, int $per_page, ?string $sort, ?string $filter): Pagerfanta
    {
        $query = $this->createQueryBuilder('t');

        if ($sort !== null) {
            $this->addSortingOption($sort, $query);
        }

        $this->addFilter($filter, $query);

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($query)
        );

        $pagerfanta->setMaxPerPage($per_page);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }

    /**
     * @throws InvalidFilterException
     * @throws InvalidOperatorException
     * @throws QueryException
     */
    public function addFilter(?string $filter, QueryBuilderAlias $query): void
    {
        if ($filter !== null) {
            $conditions = explode('&', $filter);

            $filterConditions = $this->extractFilterValues($conditions);

            $this->addCriteria($filterConditions, $query);
        }
    }

    /**
     * @param array<int,string> $conditions
     *
     * @return array<string,array<string,string>>
     *
     * @throws InvalidOperatorException
     */
    public function extractFilterValues(array $conditions): array
    {
        $filterConditions = [];

        foreach ($conditions as $condition) {
            $startOperator = strpos($condition, '[');
            $endOperator = strpos($condition, ']=');

            $filterOperator = substr($condition, $startOperator + 1, $endOperator - $startOperator - 1);
            $filterField = substr($condition, 0, $startOperator);
            $filterValue = substr($condition, $endOperator + 2, strlen($condition) - $endOperator - 2);

            $filterConditions[$filterField][$filterOperator] = $filterValue;
        }

        return $filterConditions;
    }

    /**
     * @param array<string,array<string,string>> $filterConditions
     *
     * @throws InvalidFilterException
     * @throws QueryException
     * @throws InvalidOperatorException
     */
    public function addCriteria(array $filterConditions, QueryBuilderAlias $query): void
    {
        foreach ($filterConditions as $field => $conditions) {
            $criteria = Criteria::create();

            foreach ($conditions as $operator => $value) {
                $value = $this->validateFilters($field, $value);
                $where = match ($operator) {
                    'gt' => Criteria::expr()->gt($field, $value),
                    'gte' => Criteria::expr()->gte($field, $value),
                    'lt' => Criteria::expr()->lt($field, $value),
                    'lte' => Criteria::expr()->lte($field, $value),
                    'eq' => Criteria::expr()->eq($field, $value),
                    default => throw new InvalidOperatorException('Invalid Operator')
                };

                $criteria->andWhere($where);
            }

            $query->addCriteria($criteria);
        }
    }

    private function addSortingOption(string $sort, QueryBuilderAlias $query): void
    {
        $sort_array = explode(',', $sort);
        foreach ($sort_array as $sorting) {
            $query->addOrderBy(
                't.'.substr(
                    $sorting,
                    1
                ),
                match ($sorting[0]) {
                    '+' => 'ASC',
                    default => 'DESC'
                }
            );
        }
    }

    /**
     * @throws InvalidFilterException
     */
    private function validateFilters(string $field, string $conditions): bool|string
    {
        switch ($field) {
            case 'Date':
                Carbon::parse($conditions);

                return $conditions;

            case 'isDone':
                return match ($conditions) {
                    'true' => true,
                    'false' => false,
                    default => throw new InvalidFilterException('Invalid Filter Value')
                };

            default:
                return throw new InvalidFilterException('Invalid Filter Option');
        }
    }
}
