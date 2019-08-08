<?php


use Phinx\Seed\AbstractSeed;

class EmployeesSeeder extends AbstractSeed
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
        $employees = [];
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 15; $i++) {
            $employees[] = [
                'name' => $faker->name,
                'address' => $faker->address,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'branch_id' => $faker->numberBetween(1, 5)
            ];
        }

        $this->table('employees')
            ->insert($employees)
            ->update();
    }
}
