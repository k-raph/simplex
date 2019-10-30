<?php


use Phinx\Seed\AbstractSeed;

class TagsSeeder extends AbstractSeed
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
        $faker = \Faker\Factory::create();
        $tags = [
            'PHP' => 'php',
            'NodeJS' => 'nodejs',
            'Web Development' => 'webdev',
            'Backend' => 'backend',
            'REST API' => 'rest-api'
        ];
        $data = [];

        foreach ($tags as $name => $slug) {
            $data[] = [
                'name' => $name,
                'slug' => $slug,
                'created_at' => $faker->dateTimeBetween('-2 months')->format('Y-m-d H:i:s')
            ];
        }

        $this->table('tags')
            ->insert($data)
            ->save();
    }
}
