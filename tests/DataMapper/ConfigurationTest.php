<?php

namespace Simplex\Tests\DataMapper;

use PHPUnit\Framework\TestCase;
use Simplex\DataMapper\Configuration;
use Simplex\DataMapper\Mapping\MetadataFactory;
use Simplex\Tests\DataMapper\Fixtures\Entity\User;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\Database\DatabaseInterface;
use Simplex\Database\Query\Builder;
use Simplex\DataMapper\EntityManager;
use Prophecy\Argument;

class ConfigurationTest extends TestCase
{

    /**
     * @var Configuration
     */
    private $config;

    public function setUp()
    {
        $this->config = new Configuration(__DIR__.'/Fixtures/Mapping');
        
        $db = $this->prophesize(DatabaseInterface::class);
        $qb = $this->prophesize(Builder::class);
        $qb->table(Argument::any())->willReturn($qb);
        $db->getQueryBuilder()->willReturn($qb);
        $em = $this->prophesize(EntityManager::class);
        $em->getConnection()->willReturn($db);

        $this->config->setUp($em->reveal());
    }

    public function testGetMetadataFactory()
    {
        $factory = $this->config->getMetadataFactory();
        $this->assertInstanceOf(MetadataFactory::class, $factory);
    }

    public function testMetadataCorrectlyConfigured()
    {
        $factory = $this->config->getMetadataFactory();

        $this->assertFalse($factory->hasMetadataFor(\stdClass::class));
        $this->assertTrue($factory->hasMetadataFor(User::class));

        $metadata = $factory->getClassMetadata(User::class);
        $this->assertInstanceOf(EntityMetadata::class, $metadata);
        $this->assertEquals(User::class, $metadata->getEntityClass());
    }
}
