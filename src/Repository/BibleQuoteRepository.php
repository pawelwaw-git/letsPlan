<?php

namespace App\Repository;

use App\Entity\BibleQuote;
use App\Service\Bible\RandomBibleText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BibleQuote>
 *
 * @method BibleQuote|null find($id, $lockMode = null, $lockVersion = null)
 * @method BibleQuote|null findOneBy(array $criteria, array $orderBy = null)
 * @method BibleQuote[]    findAll()
 * @method BibleQuote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BibleQuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BibleQuote::class);
    }

    public function save(BibleQuote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BibleQuote $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   public function findByBibleAndChapter(RandomBibleText $randomBibleText): ?BibleQuote
   {
       return $this->createQueryBuilder('b')
           ->andWhere('b.IdBible = :bibleId')
           ->andWhere('b.ChapterVerse = :bibleChapter')
           ->setParameter('bibleId', $randomBibleText->getId())
           ->setParameter('bibleChapter', $randomBibleText->getChapterVerse())
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

//    public function findOneBySomeField($value): ?BibleQuote
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
