<?php

namespace Simplex\Tests\Database\Query;

use PHPUnit\Framework\TestCase;
use Simplex\Database\Query\Builder;
use Simplex\Database\DatabaseInterface;

class BuilderTest extends TestCase
{

    /**
     * @var Builder
     */
    private $query;

    public function setUp()
    {
        $db = $this->getMockBuilder(DatabaseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->query = new Builder($db);
    }

    public function testSelect()
    {
        $query = $this->query
            ->select('column', 'name')
            ->table('table')
            ->where('name', 'name')
            ->getSql();
        
        $this->assertEquals(
            'SELECT column, name FROM table WHERE name = ?',
            $query
        );
        $this->assertEquals([
            0 => 'name'
        ], $this->query->getParameters());
    }

    public function testSelectAll()
    {
        $query = $this->query
            ->select()
            ->table('table')
            ->getSql();

        $this->assertEquals('SELECT * FROM table', $query);
        $this->assertEquals([], $this->query->getParameters());
    }

    public function testFromWithAlias()
    {
        $query = $this->query
            ->select('column', 'name')
            ->table('table', 't')
            ->where(['name' => 'name', 'id' => 'id'])
            ->getSql();
        
        $this->assertEquals(
            'SELECT column, name FROM table AS t WHERE name = ? AND id = ?',
            $query
        );

        $this->assertEquals([
            0 => 'name',
            1 => 'id'
        ], $this->query->getParameters());
    }

    public function testWhereWithTwoArgs()
    {
        $query = $this->query
            ->table('table')
            ->select('column')
            ->where('id', 30)
            ->getSql();

        $this->assertEquals(
            'SELECT column FROM table WHERE id = ?',
            $query
        );
        
        $this->assertEquals([
            0 => 30,
        ], $this->query->getParameters());
    }

    public function testWhereWithThreeArgs()
    {
        $query = $this->query
            ->table('table')
            ->select('column')
            ->where('id', '>', 30)
            ->getSql();

        $this->assertEquals(
            'SELECT column FROM table WHERE id > ?',
            $query
        );
        
        $this->assertEquals([
            0 => 30
        ], $this->query->getParameters());
    }

    public function testWhereWithArrayAsArg()
    {
        $query = $this->query
            ->table('table')
            ->select('column')
            ->where(['name' => 'johndoe'])
            ->where([
                ['post_id', '>', 2]
            ])
            ->getSql();

        $this->assertEquals(
            'SELECT column FROM table WHERE name = ? AND post_id > ?',
            $query
        );
        
        $this->assertEquals([
            0 => 'johndoe',
            1 => 2
        ], $this->query->getParameters());
    }

    public function testMultipleWhere()
    {
        $query = $this->query
            ->select('column')
            ->table('table')
            ->where([
                'name' => 'john',
                'id' => 'id'
            ])
            ->where('email', 'john@mail.fr')
            ->where('age', '<', 14)
            ->where([['post_id', '>', 1]])
            ->getSql();

        $this->assertEquals(
            'SELECT column FROM table WHERE name = ? AND id = ? AND email = ? AND age < ? AND post_id > ?',
            $query
        );
        
        $this->assertEquals([
            0 => 'john',
            1 => 'id',
            2 => 'john@mail.fr',
            3 => 14,
            4 => 1
        ], $this->query->getParameters());
    }

    public function testJoin()
    {
        $query = $this->query
            ->table('posts', 'p')
            ->select('p.*', 'u.title')
            ->innerJoin('users u', 'p.author_id', '=', 'u.id')
            ->where('id', 'id')
            ->getSql();
        
        $this->assertEquals(
            'SELECT p.*, u.title FROM posts AS p INNER JOIN users u ON p.author_id = u.id WHERE id = ?',
            $query
        );
        
        $this->assertEquals([
            0 => 'id'
        ], $this->query->getParameters());
    }

    public function testUpdate()
    {
        $query = $this->query
            ->table('table')
            ->where('name', 'name')
            ->update([
                'title' => 'Title',
                'post' => 'POST'
            ])
            ->getSql();

        $this->assertEquals(
            'UPDATE table SET title = ?, post = ? WHERE name = ?',
            $query
        );
        
        $this->assertEquals([
            0 => 'Title',
            1 => 'POST',
            2 => 'name'
        ], $this->query->getParameters());
    }

    public function testUpdateWithoutParams()
    {
        $query = $this->query
            ->table('table')
            ->update([
                'title' => 'Title',
                'post' => 'POST'
            ])
            ->getSql();

        $this->assertEquals(
            'UPDATE table SET title = ?, post = ?',
            $query
        );
        
        $this->assertEquals([
            0 => 'Title',
            1 => 'POST'
        ], $this->query->getParameters());
    }

    public function testInsert()
    {
        $query = $this->query
            ->table('table')
            ->insert([
                'title' => 'Title',
                'post' => 'POST'
            ])
            ->getSql();

        $this->assertEquals(
            'INSERT INTO table (title, post) VALUES(?, ?)',
            $query
        );
        
        $this->assertEquals([
            0 => 'Title',
            1 => 'POST'
        ], $this->query->getParameters());
    }

    public function testDelete()
    {
        $query = $this->query
            ->table('table')
            ->delete()
            ->getSql();
        
        $this->assertEquals(
            'DELETE FROM table',
            $query
        );
        
        $this->assertEquals([], $this->query->getParameters());
    }

    public function testDeleteWithParams()
    {
        $query = $this->query
            ->table('table')
            ->delete()
            ->where('id', 'id')
            ->getSql();
        
        $this->assertEquals(
            'DELETE FROM table WHERE id = ?',
            $query
        );
        
        $this->assertEquals([
            0 => 'id'
        ], $this->query->getParameters());
    }

    public function testSubQueryAsTable()
    {
        $sub = $this->query->newQuery()->table('table');
        $query = $this->query
            ->table($this->query->subQuery($sub))
            ->select('name')
            ->where('id', 'id')
            ->getSql();
        
        $this->assertEquals(
            'SELECT name FROM (SELECT * FROM table) WHERE id = ?',
            $query
        );
    }

    public function testSubQueryAsSelect()
    {
        $sub1 = $this->query->newQuery()->table('table1')->select('COUNT(*)');
        $sub2 = $this->query->newQuery()->table('table2')->select('COUNT(*)');
        $query = $this->query
            ->table('table')
            ->select(
                $this->query->subQuery($sub1, 'row1'),
                $this->query->subQuery($sub2, 'row2')
            )
            ->where('id', 'id')
            ->getSql();
        
        $this->assertEquals(
            'SELECT (SELECT COUNT(*) FROM table1) AS row1, (SELECT COUNT(*) FROM table2) AS row2 FROM table WHERE id = ?',
            $query
        );
    }

    public function testLimitAndOffset()
    {
        $query = $this->query
            ->table('table')
            ->limit(10)
            ->where('id', 'id')
            ->getSql();
        
        $this->assertEquals(
            'SELECT * FROM table WHERE id = ? LIMIT 0, 10',
            $query
        );
        
        $this->assertEquals([
            0 => 'id'
        ], $this->query->getParameters());
    }

    public function testGroupByWithoutOrder()
    {
        $query = $this->query
            ->table('table')
            ->where('id', 'id')
            ->orderBy('name')
            ->getSql();
        
        $this->assertEquals(
            'SELECT * FROM table WHERE id = ? ORDER BY name DESC',
            $query
        );
        
        $this->assertEquals([
            0 => 'id'
        ], $this->query->getParameters());
    }

    public function testGroupByWithOrder()
    {
        $query = $this->query
            ->table('table')
            ->where('id', 'id')
            ->orderBy('date_creation', 'ASC')
            ->getSql();
        
        $this->assertEquals(
            'SELECT * FROM table WHERE id = ? ORDER BY date_creation ASC',
            $query
        );
        
        $this->assertEquals([
            0 => 'id'
        ], $this->query->getParameters());
    }
}
