<?php

namespace Simplex\Tests\DataMapper;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Simplex\Database\DatabaseInterface;
use Simplex\Database\Query\Builder;
use Simplex\DataMapper\Configuration;
use Simplex\DataMapper\EntityManager;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\DataMapper\Repository\Repository;
use Simplex\Tests\DataMapper\Fixtures\Entity\User;

class EntityManagerTest extends TestCase
{

    /**
     * @var EntityManager
     */
    protected $em;

    public function setUp()
    {
        $db = $this->prophesize(DatabaseInterface::class);
        $qb = $this->prophesize(Builder::class);
        $qb->table(Argument::any())->willReturn($qb);
        $qb->where(Argument::any())->willReturn($qb);
        $qb->get()->willReturn([]);
        $db->getQueryBuilder()->willReturn($qb);

        $this->em = new class(new Configuration(__DIR__.'/Fixtures/Mapping'), $db->reveal()) extends EntityManager {
            public function getMetadataFor(string $className): EntityMetadata
            {
                return $this->getMapperFor($className)->getMetadata();
            }
        };

        $user = new User();
        $user->setId(1);
        $user->setName('kraph');
        $user->setEmail('kraph@email.fr');

        $this->em->persist($user);
        $this->em->flush();
    }

    public function testGetMetadataForEntityClass()
    {
        $this->expectException(\UnexpectedValueException::class);
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
        $user = new User();
        $this->em->persist($user);

        /**
         * Try getting without persist returns null
         */
        $user = $this->em->find(User::class, 2);
        $this->assertNull($user);
        
        $this->em->flush();
        
        $user = $this->em->find(User::class, 2);
        $this->assertEquals('object', gettype($user));
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(2, $user->getId());
        
        $this->assertNull($this->em->find(User::class, 3));
    }

    public function testFlushAfterEntityUpdate()
    {
        /*$user = $this->em->find(User::class, 1);
        $this->assertEquals('kraph', $user->getName());

        $user->setName('bukimi');
        $this->em->flush();

        $user = $this->em->find(User::class, 1);
        $this->assertEquals('bukimi', $user->getName());*/
    }

    public function testRemove()
    {
        $user = $this->em->find(User::class, 1);
        $this->em->remove($user);
        $this->em->flush();
        
        $this->assertNull($this->em->find(User::class, 1));
    }
}
