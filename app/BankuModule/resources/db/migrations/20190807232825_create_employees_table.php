<?php


use Phinx\Migration\AbstractMigration;

class CreateEmployeesTable extends AbstractMigration
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
        $this->table('employees')
            ->addColumn('first_name', 'string')
            ->addColumn('last_name', 'string')
            ->addColumn('address', 'string')
            ->addColumn('email', 'string')
            ->addColumn('phone', 'string')
            ->addColumn('branch_id', 'integer')
            ->addForeignKey('branch_id', 'branches', 'id')
            ->create();

        $this->table('branches')
            ->addColumn('manager_id', 'integer')
            ->addForeignKey('manager_id', 'employees', 'id')
            ->update();
    }
}
