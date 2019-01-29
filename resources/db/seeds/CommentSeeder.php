<?php

use Phinx\Seed\AbstractSeed;

class CommentSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [];

        for ($i = 0; $i < 50; $i++) {
            $data[] = [
                'content' => 'Good developers always version their code using a SCM system, so why don’t they do the same for their database
                    schema?',
                'author_id' => rand(1, 2),
                'post_id' => rand(1, 15),
                'created_at' => (new DateTime())->format('d-m-Y H:i:s')
            ];
        }

        $this->table('comments')
            ->insert($data)
            ->save();
    }
}
