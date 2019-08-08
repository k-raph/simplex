<?php


use Phinx\Seed\AbstractSeed;

class BranchesSeeder extends AbstractSeed
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
        $branches = [];
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $branches[] = [
                'name' => $faker->unique()->words(3, true),
                'city' => $faker->city,
                'country' => $faker->country,
                'phone' => $faker->phoneNumber,
                'manager_id' => $faker->numberBetween(1, 15)
            ];
        }

        $this->table('branches')
            ->insert($branches)
            ->update();
    }
}
