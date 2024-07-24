<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'subject' => $this->faker->word,
            'marks' => $this->faker->numberBetween(0, 100),
            'user_id' => User::factory(),
        ];
    }
}
