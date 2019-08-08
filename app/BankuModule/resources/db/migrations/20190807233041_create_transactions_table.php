<?php


use Phinx\Migration\AbstractMigration;

class CreateTransactionsTable extends AbstractMigration
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
        $this->table('transactions')
            ->addColumn('account_number', 'string')
            ->addColumn('timestamp', 'datetime')
            ->addColumn('employee_id', 'integer')
            ->addColumn('type', 'string')
            ->addColumn('amount', 'integer')
            ->addForeignKey('employee_id', 'employees', 'id')
            ->create();
    }
}
