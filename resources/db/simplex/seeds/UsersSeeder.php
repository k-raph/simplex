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
                'password' => 'adminpass',
                'session_token' => base64_encode(random_bytes(20))
            ], 
            [
                'username' => 'user',
                'email' => 'user@admin.fr',
                'password' => 'userpass',
                'session_token' => base64_encode(random_bytes(20))
            ]
        ];

        $table = $this->table('users');
        $table->truncate();

        $table->insert($data)->save();

    }
}
