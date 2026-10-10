<?php

namespace Database\Seeders;

use App\Models\SampleRequest;
use Illuminate\Database\Seeder;

class SampleRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SampleRequest::factory()->count(5)->create();
    }
}
