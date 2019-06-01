<?php


use Phinx\Migration\AbstractMigration;

class UpdateCommentTable extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->table('comments')
            ->dropForeignKey('author_id')
            ->removeColumn('author_id')
            ->addColumn('usr_pseudo', 'string')
            ->addColumn('usr_email', 'string')
            ->update();
    }

    public function down()
    {
        $this->table('comments')
            ->removeColumn('usr_email')
            ->removeColumn('usr_pseudo')
            ->addColumn('author_id', 'integer')
            ->addForeignKey('author_id', 'users', 'id', [
                'update' => 'CASCADE',
                'delete' => 'CASCADE'
            ])
            ->update();
    }
}
