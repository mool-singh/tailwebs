<?php

// tests/Feature/StudentTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_student_for_the_current_user()
    {
        $user = User::factory()->create();

        // Simulate authentication
        $this->actingAs($user);

        $response = $this->post('/students', [
            'name' => 'John Doe',
            'subject' => 'Math',
            'marks' => 95,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('students', [
            'name' => 'John Doe',
            'subject' => 'Math',
            'marks' => 95,
            'user_id' => $user->id, // Ensure the student is associated with the logged-in user
        ]);
    }

    #[Test]
    public function it_updates_a_student_for_the_current_user()
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        // Simulate authentication
        $this->actingAs($user);

        $response = $this->put("/students/{$student->id}", [
            'name' => 'Jane Doe',
            'subject' => 'Science',
            'marks' => 90,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Jane Doe',
            'subject' => 'Science',
            'marks' => 90,
            'user_id' => $user->id, // Ensure the student is still associated with the logged-in user
        ]);
    }

    #[Test]
    public function it_does_not_update_students_of_other_users()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $otherUser->id]);

        // Simulate authentication
        $this->actingAs($user);

        $response = $this->put("/students/{$student->id}", [
            'name' => 'Jane Doe',
            'subject' => 'Science',
            'marks' => 90,
        ]);

        $response->assertStatus(403); // Forbidden
    }

    #[Test]
    public function it_deletes_a_student_for_the_current_user()
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        // Simulate authentication
        $this->actingAs($user);

        $response = $this->delete("/students/{$student->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
            'user_id' => $user->id, // Ensure the student record is deleted
        ]);
    }

    #[Test]
    public function it_does_not_delete_students_of_other_users()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $otherUser->id]);

        // Simulate authentication
        $this->actingAs($user);

        $response = $this->delete("/students/{$student->id}");

        $response->assertStatus(403); // Forbidden
    }
}


