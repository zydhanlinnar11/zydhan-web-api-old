<?php

namespace Database\Seeders;

use App\Models\SocialProvider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class GithubSocialProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'name' => 'github',
            'client_id' => Crypt::encrypt(env('GITHUB_CLIENT_ID')),
            'client_secret' => Crypt::encrypt(env('GITHUB_CLIENT_SECRET')),
        ];

        // Modify if exists
        $current = SocialProvider::where('name', 'github')->first();
        if ($current) {
            $current->fill($data)->save();
            return;
        }

        SocialProvider::create($data);
    }
}
