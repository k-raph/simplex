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
        $data = [
            [
                'username' => 'admin',
                'email' => 'admin@admin.fr',
                'password' => 'adminpass'
            ], 
            [
                'username' => 'user',
                'email' => 'user@admin.fr',
                'password' => 'userpass'
            ]
        ];

        $this->table('users')
            ->insert($data)
            ->save();

    }
}
