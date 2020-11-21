<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recette[]    findAll()
 * @method Recette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recette::class);
    }

    /**
     * @return Recette[] Returns an array of Recette objects
     */
    public function findByCategory($name)
    {
        return array_values(array_filter($this->findAll(), function(Recette $recette) use ($name){
            return $recette->getCategory()->getName() === $name;
        }));
    }


    public function findByKeyword($keyword): ?array
    {
        return   $query = $this->getEntityManager()->createQuery(
            'SELECT r
                FROM App\Entity\Recette r
                INNER JOIN r.category c
                INNER JOIN r.ingredients i
                INNER JOIN r.season s
                WHERE r.title LIKE :keyword
                OR c.name LIKE :keyword
                OR i.content LIKE :keyword
                OR s.name LIKE :keyword'
        )->setParameter('keyword', "%" . $keyword . "%")->getResult();
    }

}
