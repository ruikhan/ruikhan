<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;

class DoctorSeeder extends Seeder
{
    public function run()
    {
        Doctor::create([
            'name' => 'Dr. Sarah Smith',
            'specialization' => 'Cardiology',
            'availability' => ['Mon', 'Wed', 'Fri'], // This works because of the cast in your Model
            'avatar' => null,
        ]);

        Doctor::create([
            'name' => 'Dr. James Wilson',
            'specialization' => 'General Practice',
            'availability' => ['Tue', 'Thu'],
            'avatar' => null,
        ]);
        
        Doctor::create([
            'name' => 'Dr. Emily Chen',
            'specialization' => 'Pediatrics',
            'availability' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            'avatar' => null,
        ]);
    }
}