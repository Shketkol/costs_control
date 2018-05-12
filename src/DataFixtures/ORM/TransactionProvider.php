<?php


namespace App\DataFixtures\ORM;

use App\Entity\Account;
use App\Entity\TransactionType;
use App\Entity\User;
use Faker\Provider\Base as BaseProvider;
use Faker\Generator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TransactionProvider extends BaseProvider implements ContainerAwareInterface
{
    /**
    * @var ContainerInterface
    */
    private $container;

    public function __construct(Generator $generator, ContainerInterface $container = null)
    {
        parent::__construct($generator);
        $this->container = $container;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function transactionUser()
    {
        // Find my test user
        $testUser = $this->container->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['username' => 'timoffmax']);
        ;

        return $testUser;
    }

    public function transactionAccount()
    {
        // Find my test user
        $testUser = $this->container->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['username' => 'timoffmax']);
        ;

        // Get accounts
        $account = $this->container->get('doctrine')
            ->getRepository(Account::class)
            ->find(random_int(1, 2));
        ;

        // Return random account
        return random_int(1, 2);
    }

    public function transactionType()
    {
        // Get transaction types
        $types = $this->container->get('doctrine')
            ->getRepository(TransactionType::class)
            ->findAll();
        ;

        // Return random account
        return random_int(1, 2);
    }

    public function transactionSum()
    {
        // Return random sum
        return random_int(10, 1000);
    }

    public function transactionComment()
    {
        return 'Fixture';
    }
}