<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schools')->insert([
            'name' => 'School ABC',
            'email' => 'schoolabc@gmail.com',
            'address' => 'jakarta',
            'password' => Hash::make('test123')
        ]);

        DB::table('users')->insert([
            'school_id' => '1',
            'name' => 'Budi',
            'email' => 'budi@gmail.com',
            'phone_number' => '08213939',
            'role' => 'student',
            'password' => Hash::make('test123')
        ],[
            'school_id' => '1',
            'name' => 'Andi',
            'email' => 'andi@gmail.com',
            'phone_number' => '08213939',
            'role' => 'teachers',
            'password' => Hash::make('test123')
        ]);

        DB::table('users')->insert([
            'school_id' => '1',
            'name' => 'Andi',
            'email' => 'andi@gmail.com',
            'phone_number' => '08213939',
            'role' => 'teachers',
            'password' => Hash::make('test123')
        ]);

        DB::table('teachers')->insert([
            'id' => '1',
        ]);

        DB::table('school_classes')->insert([
            'school_id' => '1',
            'teacher_id' => '1',
            'class_code' => 'MATH09133',
            'class_term' => 'Odd',
        ]);

        DB::table('students')->insert([
            'id' => '1',
            'school_class_id' => '1',
        ]);


        DB::table('courses')->insert([
            'name' => 'Mathematic',
        ]);

        DB::table('schedules')->insert([
            'school_id' => '1',
            'course_id' => '1',
            'teacher_id' => '1',
            'day' => 'Monday',
            'start_time' => '07:00',
            'end_time' => '09:00',
        ]);
    }
}
