<?php

namespace App\DataFixtures;

use App\Entity\Note;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;

/**
 * Class NotesFixture
 */
class NotesFixture extends AbstractFixture
{
    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(
            Note::class,
            rand(20, 50),
            function (Note $note, int $index) {
                $date = new DateTimeImmutable();
                $note
                    ->setTitle($this->faker->text())
                    ->setContent($this->faker->paragraphs(rand(3, 10), true))
                    ->setCreatedAt($date)
                    ->setUpdatedAt($date);
            }
        );
    }
}