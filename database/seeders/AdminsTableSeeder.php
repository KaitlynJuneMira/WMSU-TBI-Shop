<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRecords = [
            ['id'=>2,'name'=>'John','type'=>'vendor','vendor_id'=>1,'mobile'=>'97000000000','email'=>'john@admin.com','password'=>'$2a$12$99YxoNEWP3RTBaQ.yZ3mSewgYJFUAxP2DTyD/jki9f/473usoC5fK','image'=>'','status'=>0],
        ];
        Admin::insert($adminRecords);
    }
}
