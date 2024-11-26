<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tạo một người dùng với thông tin cụ thể
        User::create([
            'name' => 'Dương Hồng Nhung',
            'email' => 'dungnhung1209@gmail.com',
            'password' => Hash::make('dungnhung'), // Đặt mật khẩu của bạn ở đây
            'user_catalogue_id' => '1'
        ]);
    }
}
