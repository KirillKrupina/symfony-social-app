<?php

namespace App\Repository;

use App\Entity\MicroPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MicroPost>
 *
 * @method MicroPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method MicroPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method MicroPost[]    findAll()
 * @method MicroPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MicroPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    public function add(MicroPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MicroPost $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllWithComments(): array
    {
        return $this->findAllQuery(
            isWithComments: true
        )->getQuery()
            ->getResult();
    }

    public function findAllByAuthor(int $author): array
    {
        return $this->findAllQuery(
            isWithComments: true,
            isWithLikes: true,
            isWithAuthor: true
        )->where('p.author = :author')
            ->setParameter('author', $author)
            ->getQuery()
            ->getResult();
    }

    private function findAllQuery(
        bool $isWithComments = false,
        bool $isWithLikes = false,
        bool $isWithAuthor = false
    ): QueryBuilder
    {
        $query = $this->createQueryBuilder('p');

        if ($isWithComments) {
            $query
                ->leftJoin('p.comments', 'c')
                ->addSelect('c');
        }
        if ($isWithLikes) {
            $query->leftJoin('p.likedBy', 'l')
                ->addSelect('l');
        }
        if ($isWithAuthor) {
            $query->leftJoin('p.author', 'a')
                ->addSelect('a');
            $query->leftJoin('a.userProfile', 'up')
                ->addSelect('up');
        }

        return $query->orderBy('p.created', 'DESC');
    }

//    /**
//     * @return MicroPost[] Returns an array of MicroPost objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MicroPost
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
