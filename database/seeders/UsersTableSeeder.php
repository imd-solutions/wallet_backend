<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        if (User::count() == 0) {

            $user = User::create([
                'name'           => 'Admin User',
                'email'          => 'admin@admin.com',
                'password'       => bcrypt('password'),
                'remember_token' => Str::random(60),
            ]);

            $name = explode(' ', $user->name);

            Profile::create([
                'user_id' => $user->id,
                'firstname' => $name[0],
                'lastname' => $name[1]
            ]);
        }
    }
}
