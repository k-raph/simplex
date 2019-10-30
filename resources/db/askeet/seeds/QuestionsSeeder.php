<?php


use Phinx\Seed\AbstractSeed;

class QuestionsSeeder extends AbstractSeed
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

        for ($i = 0; $i < 15; $i++) {
            $date = $faker->dateTimeBetween('-2 months')->format('Y-m-d H:i:s');
            $data[] = [
                'title' => $faker->sentence(),
                'slug' => $faker->slug(),
                'content' => $faker->realText(),
                'votes' => $faker->numberBetween(0, 20),
                'best_answer' => null,
                'author_id' => rand(1, 5),
                'created_at' => $date,
                'updated_at' => $date
            ];
        }

        $this->table('questions')
            ->insert($data)
            ->save();
    }
}
