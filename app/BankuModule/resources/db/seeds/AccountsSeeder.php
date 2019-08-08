<?php


use Phinx\Seed\AbstractSeed;

class AccountsSeeder extends AbstractSeed
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
        $accounts = [];
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 30; $i++) {
            $accounts[] = [
                'number' => $faker->bankAccountNumber,
                'branch_id' => $faker->numberBetween(1, 5),
                'customer_id' => $faker->numberBetween(1, 30),
                'type' => $faker->randomElement(['SAVING', 'CURRENT', 'CHECKING']),
                'balance' => $faker->randomNumber(6),
                'interest_rate' => $faker->randomFloat(2, 0, 10),
                'status' => $faker->randomElement(['ENABLED', 'DISABLED'])
            ];
        }

        $this->table('accounts')
            ->insert($accounts)
            ->update();
    }
}
