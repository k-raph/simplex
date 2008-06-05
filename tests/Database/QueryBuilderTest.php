<?php

namespace Simplex\Tests\Database;

use PHPUnit\Framework\TestCase;
use Simplex\Database\QueryBuilder;
use Simplex\Database\Connection;

class QueryBuilderTest extends TestCase
{

    public function setUp()
    {
        $db = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->qb = new QueryBuilder($db);
    }

    public function testSelect()
    {
        $query = $this->qb
            ->select('column', 'name')
            ->from('table')
            ->where('name = :name')
            ->getSql();
        
        $this->assertEquals(
            'SELECT column,name FROM table WHERE name = :name',
            $query);
    }

    public function testSelectAll()
    {
        $query = $this->qb
            ->select()
            ->from('table')
            ->getSql();

        $this->assertEquals('SELECT * FROM table', $query);
    }

    public function testFromWithAlias()
    {
        $query = $this->qb
            ->select('column', 'name')
            ->from('table', 't')
            ->where('name = :name', 'id = :id')
            ->getSql();
        
        $this->assertEquals(
            'SELECT column,name FROM table AS t WHERE name = :name AND id = :id',
            $query);
    }

    public function testMultipleWhere()
    {
        $query = $this->qb
            ->select('column')
            ->from('table')
            ->where('name = :name')
            ->where('id = :id')
            ->getSql();
        
        $this->assertEquals(
            'SELECT column FROM table WHERE name = :name AND id = :id',
            $query);
    }

    public function testJoin()
    {
        $query = $this->qb
            ->select('p.*', 'u.title')
            ->from('posts', 'p')
            ->join('users', 'u')
            ->on('p.author_id = u.id')
            ->where('id = :id')
            ->getSql();
        
        $this->assertEquals(
            'SELECT p.*,u.title FROM posts AS p INNER JOIN users u ON p.author_id = u.id WHERE id = :id',
            $query
        );
    }
}
