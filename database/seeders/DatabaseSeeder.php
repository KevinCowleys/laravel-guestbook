<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create();

        $this->command->info('Creating Admin Account...');

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@fake.com',
            'password' => app('hash')->make('password'),
            'is_admin' => true,
        ]);

        $this->command->info('Admin Account Created.');


        $this->command->info('Seeding Users...');

        User::factory(100)->create();

        $users = User::all();

        foreach ($users as $user) {
            if ($user->name == 'admin') {
                return;
            }

            $message = Message::create([
                'user_id' => $user->id,
                'comment' => $faker->sentence(rand(5, 50)),
            ]);

            for ($x = 0; $x <= rand(0, 40); $x++) {
                $is_admin = rand(0, 1);
                if ($is_admin === 0) {
                    Comment::create([
                        'comment' => $faker->sentence(rand(5, 50)),
                        'user_id' => $admin->id,
                        'message_id' => $message->id,
                    ]);
                } else {
                    Comment::create([
                        'comment' => $faker->sentence(rand(5, 50)),
                        'user_id' => $user->id,
                        'message_id' => $message->id,
                    ]);
                }
            }
        }
    }
}
