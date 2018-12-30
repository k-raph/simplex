<?php

namespace Simplex\Tests\Database;

use PHPUnit\Framework\TestCase;
use Simplex\Database\QueryBuilder;
use Simplex\Database\Connection;

class QueryBuilderTest extends TestCase
{

    // public function setUp()
    // {
    //     $db = $this->getMockBuilder(Connection::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //     $this->qb = new QueryBuilder($db);
    // }

    // public function testSelect()
    // {
    //     $query = $this->qb
    //         ->select('column', 'name')
    //         ->table('table')
    //         ->where('name = :name')
    //         ->getSql();
        
    //     $this->assertEquals(
    //         'SELECT column,name FROM table WHERE name = :name',
    //         $query);
    // }

    // public function testSelectAll()
    // {
    //     $query = $this->qb
    //         ->select()
    //         ->table('table')
    //         ->getSql();

    //     $this->assertEquals('SELECT * FROM table', $query);
    // }

    // public function testFromWithAlias()
    // {
    //     $query = $this->qb
    //         ->select('column', 'name')
    //         ->table('table', 't')
    //         ->where('name = :name', 'id = :id')
    //         ->getSql();
        
    //     $this->assertEquals(
    //         'SELECT column,name FROM table AS t WHERE name = :name AND id = :id',
    //         $query);
    // }

    // public function testMultipleWhere()
    // {
    //     $query = $this->qb
    //         ->select('column')
    //         ->table('table')
    //         ->where('name = :name')
    //         ->where('id = :id')
    //         ->getSql();
        
    //     $this->assertEquals(
    //         'SELECT column FROM table WHERE name = :name AND id = :id',
    //         $query);
    // }

    // public function testJoin()
    // {
    //     $query = $this->qb
    //         ->table('posts', 'p')
    //         ->select('p.*', 'u.title')
    //         ->join('users', 'u')
    //         ->on('p.author_id = u.id')
    //         ->where('id = :id')
    //         ->getSql();
        
    //     $this->assertEquals(
    //         'SELECT p.*,u.title FROM posts AS p INNER JOIN users u ON p.author_id = u.id WHERE id = :id',
    //         $query
    //     );
    // }

    // public function testUpdate()
    // {
    //     $query = $this->qb
    //         ->table('table')
    //         ->where('name = :name')
    //         ->update([
    //             'title' => 'Title',
    //             'post' => 'POST'
    //         ])
    //         ->getSql();

    //     $this->assertEquals(
    //         'UPDATE table SET title = :title, post = :post WHERE name = :name',
    //         $query
    //     );
    // }

    // public function testUpdateWithoutParams()
    // {
    //     $query = $this->qb
    //         ->table('table')
    //         ->update([
    //             'title' => 'Title',
    //             'post' => 'POST'
    //         ])
    //         ->getSql();

    //     $this->assertEquals(
    //         'UPDATE table SET title = :title, post = :post',
    //         $query
    //     );
    // }

    // public function testInsert()
    // {
    //     $query = $this->qb
    //         ->table('table')
    //         ->insert([
    //             'title' => 'Title',
    //             'post' => 'POST'
    //         ])
    //         ->getSql();

    //     $this->assertEquals(
    //         'INSERT INTO table (title, post) VALUES(:title, :post)',
    //         $query
    //     );
    // }

    // public function testDelete()
    // {
    //     $query = $this->qb
    //         ->table('table')
    //         ->delete()
    //         ->getSql();
        
    //     $this->assertEquals(
    //         'DELETE FROM table',
    //         $query
    //     );
    // }

    // public function testDeleteWithParams()
    // {
    //     $query = $this->qb
    //         ->table('table')
    //         ->delete()
    //         ->where('id = :id')
    //         ->getSql();
        
    //     $this->assertEquals(
    //         'DELETE FROM table WHERE id = :id',
    //         $query
    //     );
    // }
}
