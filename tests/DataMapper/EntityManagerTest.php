<?php

namespace Simplex\Tests\DataMapper;

use PHPUnit\Framework\TestCase;
use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Configuration;
use Simplex\DataMapper\Repository\Repository;
use Simplex\Tests\DataMapper\Fixtures\Entity\User;
use Simplex\DataMapper\Mapping\EntityMetadata;

class EntityManagerTest extends TestCase
{

    /**
     * @var EntityManager
     */
    protected $em;

    public function setUp()
    {
        $this->em = new EntityManager(Configuration::setup(__DIR__.'/Fixtures/Mapping'));
    }

    public function testGetMetadataForEntityClass()
    {
        $meta = $this->em->getMetadataFor(\stdClass::class);
        $this->assertNull($meta);

        $meta = $this->em->getMetadataFor(User::class);
        $this->assertInstanceOf(EntityMetadata::class, $meta);
    }

    public function testGetRepository()
    {
        $repo = $this->em->getRepository(User::class);
        $this->assertInstanceOf(Repository::class, $repo);
    }

    public function testFindWhenExistsReturnObject()
    {
        // $user = $this->em->find(User::class, 1);

        // $this->assertEquals('object', gettype($user));
        // $this->assertInstanceOf(User::class, $user);
        // $this->assertEquals(1, $user->getId());
    }
}
