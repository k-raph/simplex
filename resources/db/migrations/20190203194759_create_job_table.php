<?php


use Phinx\Migration\AbstractMigration;

class CreateJobTable extends AbstractMigration
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
        $this->table('jobs')
            ->addColumn('company', 'string')
            ->addColumn('type', 'string')
            ->addColumn('url', 'string')
            ->addColumn('position', 'string')
            ->addColumn('location', 'string')
            ->addColumn('email', 'string')
            ->addColumn('category_id', 'integer')
            ->addColumn('description', 'text')
            ->addColumn('application', 'text')
            ->addColumn('is_public', 'boolean')
            ->addColumn('logo', 'string')
            ->addColumn('token', 'string')
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('expires_at', 'datetime')
            ->addForeignKey('category_id', 'categories', 'id', [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ])
            ->create();
    }
}
