<?php

namespace Database\Seeders;

use App\Models\SocialProvider;
use Illuminate\Database\Seeder;

class GithubSocialProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SocialProvider::create(['name' => 'github']);
    }
}
