<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\SalesChannel;
use Illuminate\Database\Seeder;

class SalesChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalesChannel::firstOrCreate(['name' => 'Website']);
        SalesChannel::firstOrCreate(['name' => 'Instagram']);
        SalesChannel::firstOrCreate(['name' => 'WhatsApp']);
        SalesChannel::firstOrCreate(['name' => 'Boutique Sale']);
        SalesChannel::firstOrCreate(['name' => 'Other']);
    }
}
