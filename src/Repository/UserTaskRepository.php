<?php

namespace App\Repository;

use App\Entity\UserTask;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserTask|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTask|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTask[]    findAll()
 * @method UserTask[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTask::class);
    }

    /**
     * Returns an array of UserTask objects where date is smaller than actual date
     * @param string $actualDate 
     * @return UserTask[] 
     */
    public function findAllSmallerThanActualDateByUserId($actualDate, $userId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.taskStartDate < :actualDate')
            ->andWhere('u.taskIdOwnerUser = :userId')
            ->setParameter('actualDate', $actualDate)
            ->setParameter('userId', $userId)
            ->orderBy('u.taskStartDate', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Returns an array of UserTask objects where date is bigger than actual date
     * @param string $actualDate 
     * @return UserTask[] 
     */
    public function findAllBiggerThanActualDateByUserId($actualDate, $userId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.taskStartDate > :actualDate')
            ->andWhere('u.taskIdOwnerUser = :userId')
            ->setParameter('actualDate', $actualDate)
            ->setParameter('userId', $userId)
            ->orderBy('u.taskStartDate', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Returns an array of UserTask objects where date is smaller than actual date
     * @param string $actualDate 
     * @return UserTask[] 
     */
    public function findAllSmallerOrEgualThanActualDate($actualDate): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.taskStartDate <= :actualDate')
            ->setParameter('actualDate', $actualDate)
            ->orderBy('u.taskStartDate', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * Returns an array with all UserTask objects where date is smaller than actual date for sending email
     * @param string $actualDate 
     * @param string $todo task statut
     * @param string $late task statut
     * @param string $numberAlertMax
     * 
     * @return UserTask[] 
     */
    public function findAllTaskbyUserWithPastDate($userId, $todo, $late): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.taskIdOwnerUser = :userId')
            ->andWhere('u.taskStatut = :toDo OR u.taskStatut = :late')
            ->setParameter('userId', $userId)
            ->setParameter('toDo', $todo)
            ->setParameter('late', $late)
            ->orderBy('u.taskStartDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
