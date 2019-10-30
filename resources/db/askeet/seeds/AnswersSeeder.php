<?php

use Phinx\Seed\AbstractSeed;

class AnswersSeeder extends AbstractSeed
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
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $date = $faker->dateTimeBetween('-2 months')->format('Y-m-d H:i:s');
            $data[] = [
                'content' => $faker->realText(),
                'votes' => $faker->numberBetween(0, 10),
                'is_best' => false,
                'parent_id' => $faker->numberBetween(1, 15),
                'author_id' => rand(1, 2),
                'created_at' => $date,
                'updated_at' => $date
            ];
        }

        $this->table('answers')
            ->insert($data)
            ->save();
    }
}
