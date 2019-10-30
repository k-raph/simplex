<?php


use Phinx\Seed\AbstractSeed;

class QuestionTagSeeder extends AbstractSeed
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
        for ($i = 0; $i < 15; $i++) {
            $data[] = [
                'question_id' => rand(1, 15),
                'tag_id' => rand(1, 5)
            ];
        }

        $this->table('question_tag')
            ->insert($data)
            ->save();
    }
}
