<?php

namespace Simplex\Tests\DataMapper\Mapping;

use PHPUnit\Framework\TestCase;
use Simplex\DataMapper\Mapping\EntityMetadata;
use Simplex\Tests\DataMapper\Fixtures\Entity\User;
use Simplex\DataMapper\Repository\Repository;

class EntityMetadataTest extends TestCase
{
    public function testCorrectMetadataInfos()
    {
        $path = require(dirname(__DIR__).'/Fixtures/Mapping/UserMap.php');
        $metadata = new EntityMetadata(User::class, $path[User::class]);

        $this->assertEquals(User::class, $metadata->getEntityClass());
        $this->assertEquals(Repository::class, $metadata->getRepositoryClass());

        $this->assertEquals('users', $metadata->getTableName());
        $this->assertEquals('int', $metadata->getColumnType('id'));
        $this->assertEquals('string', $metadata->getColumnType('name'));
        $this->assertEquals('id', $metadata->getIdentifier());
        
        $this->assertEquals(['id', 'name', 'email', 'password'], $metadata->getNames());
        $this->assertEquals(['id', 'username', 'email', 'password'], $metadata->getSQLNames());
        
        $this->assertEquals('username', $metadata->getSQLName('name'));
        $this->assertEquals('email', $metadata->getSQLName('email'));
    }
}
