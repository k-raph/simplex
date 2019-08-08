<?php


use Phinx\Migration\AbstractMigration;

class CreateAccountsTable extends AbstractMigration
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
        $this->table('accounts')
            ->addColumn('number', 'string')
            ->addColumn('type', 'string')
            ->addColumn('balance', 'integer')
            ->addColumn('interest_rate', 'integer')
            ->addColumn('status', 'string')
            ->addColumn('branch_id', 'integer')
            ->addColumn('customer_id', 'integer')
            ->addForeignKey('branch_id', 'branches', 'id')
            ->addForeignKey('customer_id', 'customers', 'id')
            ->create();
    }
}
