<?php

namespace App\Repository;

use App\Entity\Turnament;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Turnament>
 *
 * @method Turnament|null find($id, $lockMode = null, $lockVersion = null)
 * @method Turnament|null findOneBy(array $criteria, array $orderBy = null)
 * @method Turnament[]    findAll()
 * @method Turnament[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TurnamentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Turnament::class);
    }

    public function save(Turnament $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Turnament $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
