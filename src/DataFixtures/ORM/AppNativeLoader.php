<?php

namespace App\DataFixtures\ORM;

use Nelmio\Alice\Loader\NativeLoader;
use Faker\Generator as FakerGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppNativeLoader extends NativeLoader
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct(null);
    }

    protected function createFakerGenerator(): FakerGenerator
    {
        $generator = parent::createFakerGenerator();
        $generator->addProvider(new TransactionProvider($generator, $this->container));
        return $generator;
    }
}
