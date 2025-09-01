<?php

namespace App\DataFixtures;

use App\Domain\Entity\Email;
use App\Domain\Entity\EmailEvent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class EmailFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();

        // Store all tracking IDs to use in events
        $emails = [];

        // Create 20 fake emails
        for ($i = 0; $i < 20; $i++) {
            $email = new Email();
            $email->setRecipient($faker->email())
                  ->setSubject($faker->sentence())
                  ->setBody($faker->paragraph())
                  ->setCreatedAt(new \DateTimeImmutable())
                  ->setCampaignId($faker->randomElement(['cmp-aug-2025','cmp-sept-2025','cmp-oct-2025']));

            $manager->persist($email);
            $emails[] = $email;
        }

        // Create events linked to emails
        foreach ($emails as $email) {

            $eventCount = rand(1, 5);

            for ($j = 0; $j < $eventCount; $j++) {
                $event = new EmailEvent();
                $event->setTrackingId($email->getTrackingId())
                      ->setType($faker->randomElement(['open','click']))
                      ->setOccuredAt(\DateTimeImmutable::createFromMutable($faker->dateTime('-10 days')))
                      ->setMeta($email->getCampaignId());

                $manager->persist($event);
            }
        }

        $manager->flush();
    }
}
