<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Group::create(['identifier' => '120363392522734779@g.us', 'name' => 'Group 1 UGM', 'is_cust' => true]);
        Group::create(['identifier' => '120363418627501111@g.us', 'name' => 'Group 2 UGM', 'is_cust' => true]);
        Group::create(['identifier' => '120363403842414118@g.us', 'name' => 'Group 3 UGM', 'is_cust' => true]);
    }
}
