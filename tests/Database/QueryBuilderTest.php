<?php

namespace Simplex\Tests\Database;

use PHPUnit\Framework\TestCase;
use Simplex\Database\QueryBuilder;

class QueryBuilderTest extends TestCase
{

    public function testSelect()
    {
        $query = (new QueryBuilder())
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
        $query = (new QueryBuilder())
            ->select()
            ->from('table')
            ->getSql();

        $this->assertEquals('SELECT * FROM table', $query);
    }

    public function testFromWithAlias()
    {
        $query = (new QueryBuilder())
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
        $query = (new QueryBuilder())
            ->select('column')
            ->from('table')
            ->where('name = :name')
            ->where('id = :id')
            ->getSql();
        
        $this->assertEquals(
            'SELECT column FROM table WHERE name = :name AND id = :id',
            $query);
    }
}
