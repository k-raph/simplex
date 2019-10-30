<?php


use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
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
        $data = [];

        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'username' => $faker->unique(true)->userName,
                'email' => $faker->email,
                'password' => $faker->password,
                'session_token' => $faker->sha256,
                'role' => $faker->randomElement(['ROLE_ADMIN', 'ROLE_USER', 'ROLE_MODERATOR'])
            ];
        }

        $table = $this->table('users');

        $table->insert($data)->save();

    }
}
