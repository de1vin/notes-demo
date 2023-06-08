<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;


/**
 * Class BaseFixture
 */
abstract class AbstractFixture extends Fixture
{
    private ObjectManager $manager;
    protected Faker\Generator $faker;

    /**
     * @param ObjectManager $manager
     */
    abstract protected function loadData(ObjectManager $manager): void;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Faker\Factory::create();

        $this->loadData($manager);
    }

    /**
     * @param string   $className
     * @param int      $count
     * @param callable $factory
     */
    protected function createMany(string $className, int $count, callable $factory): void
    {
        for ($index = 0; $index < $count; $index++) {
            $entity = new $className();
            $factory($entity, $index);
            $this->manager->persist($entity);
        }

        $this->manager->flush();
    }
}
