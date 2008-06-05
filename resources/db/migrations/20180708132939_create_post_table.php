<?php


use Phinx\Migration\AbstractMigration;

class CreatePostTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        if ($this->hasTable('posts')) $this->dropTable('posts');

        $this->table('posts')
            ->addColumn('title', 'string')
            ->addColumn('slug', 'string')
            ->addColumn('content', 'string')
            ->addColumn('author_id', 'integer')
            ->addIndex('slug', ["unique" => true])
            ->addForeignKey('author_id', 'users', 'id', [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ])
            ->create();
    }
}
