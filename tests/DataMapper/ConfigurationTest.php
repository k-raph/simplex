<?php

namespace Simplex\Tests\DataMapper;

use PHPUnit\Framework\TestCase;
use Simplex\DataMapper\Configuration;
use Simplex\DataMapper\Mapping\MetadataFactory;
use Simplex\Tests\DataMapper\Fixtures\Entity\User;
use Simplex\DataMapper\Mapping\EntityMetadata;

class ConfigurationTest extends TestCase
{

    /**
     * @var Configuration
     */
    private $config;

    public function setUp()
    {
        $this->config = Configuration::setup(__DIR__.'/Fixtures/Mapping');
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
