<?php


use Phinx\Seed\AbstractSeed;

class CustomersSeeder extends AbstractSeed
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
        $customers = [];
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 30; $i++) {
            $customers[] = [
                'name' => $faker->name,
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'email' => $faker->email,
                'date_of_birth' => $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d H:i:s'),
                'branch_id' => $faker->numberBetween(1, 5),
                'joined_at' => $faker->dateTimeThisDecade()->format('Y-m-d H:i:s')
            ];
        }

        $this->table('customers')
            ->insert($customers)
            ->update();
    }
}
