<?php

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseWebTestCase extends WebTestCase
{
    protected ?\Symfony\Bundle\FrameworkBundle\KernelBrowser $client = null;
    protected ?EntityManagerInterface $em = null;

    protected static bool $schemaCreated = false;

    protected function setUp(): void
    {
        parent::setUp();


        $this->client = static::createClient();


        $this->em = self::getContainer()->get(EntityManagerInterface::class);


        if (!self::$schemaCreated) {
            $schemaTool = new SchemaTool($this->em);
            $metadata = $this->em->getMetadataFactory()->getAllMetadata();

            if (!empty($metadata)) {

                $schemaTool->dropSchema($metadata);
                $schemaTool->createSchema($metadata);
            }

            self::$schemaCreated = true;
        }


        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {

        if ($this->em->getConnection()->isTransactionActive()) {
            $this->em->getConnection()->rollBack();
        }

        parent::tearDown();


        $this->em->close();
        $this->em = null;
        $this->client = null;
    }
}
