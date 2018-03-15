<?php

namespace App\Repository;

use App\Entity\AccountType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class AccountTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AccountType::class);
    }

    public function findCommonAndUserTypes(UserInterface $user)
    {
        return $this->createQueryBuilder('a')
            ->where('a.user IS NULL')
            ->orWhere('a.user = :user')
            ->setParameter('user', $user)
            ->orderBy('a.user', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
