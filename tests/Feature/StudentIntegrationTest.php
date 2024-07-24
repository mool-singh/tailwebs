<?php

// tests/Feature/StudentIntegrationTest.php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class StudentIntegrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_and_associates_students_with_the_current_user()
    {
        $user = User::factory()->create();

        // Simulate authentication
        $this->actingAs($user);

        // Create a student
        $response = $this->post('/students', [
            'name' => 'Alice Smith',
            'subject' => 'Biology',
            'marks' => 85,
        ]);

        $response->assertStatus(201);
        $student = Student::where('name', 'Alice Smith')->first();
        $this->assertNotNull($student);
        $this->assertEquals($user->id, $student->user_id);
    }

    #[Test]
    public function it_allows_user_to_update_their_own_students()
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        // Simulate authentication
        $this->actingAs($user);

        // Update student
        $response = $this->put("/students/{$student->id}", [
            'name' => 'Alice Johnson',
            'subject' => 'Physics',
            'marks' => 90,
        ]);

        $response->assertStatus(200);
        $student->refresh();
        $this->assertEquals('Alice Johnson', $student->name);
        $this->assertEquals('Physics', $student->subject);
        $this->assertEquals(90, $student->marks);
    }

    #[Test]
    public function it_prevents_users_from_updating_other_users_students()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $otherUser->id]);

        // Simulate authentication
        $this->actingAs($user);

        // Attempt to update another user's student
        $response = $this->put("/students/{$student->id}", [
            'name' => 'Unauthorized Update',
            'subject' => 'History',
            'marks' => 75,
        ]);

        $response->assertStatus(403); // Forbidden
    }

    #[Test]
    public function it_allows_user_to_delete_their_own_students()
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        // Simulate authentication
        $this->actingAs($user);

        // Delete student
        $response = $this->delete("/students/{$student->id}");

        // Check for successful response
        $response->assertStatus(200);

        // Check that the student has been deleted
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    #[Test]
    public function it_prevents_users_from_deleting_other_users_students()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $otherUser->id]);

        // Simulate authentication
        $this->actingAs($user);

        // Attempt to delete another user's student
        $response = $this->delete("/students/{$student->id}");

        $response->assertStatus(403); // Forbidden
    }
}


