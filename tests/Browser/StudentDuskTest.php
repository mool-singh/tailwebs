<?php
namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;

class StudentDuskTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function a_user_can_create_a_student()
    {
        $user = User::factory()->create(); // Create a user

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user) // Log in the user
                    ->visit('/students')
                    ->click('#openModalButton')
                    ->waitFor("#studentModal",3)
                    ->type('name', 'New Student')
                    ->type('subject', 'Math')
                    ->type('marks', '85')
                    ->click('#saveStudentButton') // Ensure the save button has appropriate text or selector
                    ->waitFor("#successText",2)
                    ->assertSee('Student added successfully')
                    ->waitForText("New Student",4)
                    ->assertSee('New Student'); // Ensure the student is visible in the list
        });
    }

    #[Test]
    public function a_user_can_update_their_own_student()
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user, $student) {
            $browser->loginAs($user)
                    ->visit('/students')
                    ->waitFor('.editButton',3)
                    ->click('.editButton[data-id="'.$student->id.'"]') // Use class and data attribute for edit button
                    ->waitFor("#studentModal",3)
                    ->type('name', 'Updated Student Name')
                    ->type('subject', 'Science')
                    ->type('marks', '90')
                    ->click('#saveStudentButton') // Ensure the update button has appropriate text or selector
                    ->waitFor("#successText",2)
                    ->assertSee('Student updated successfully')
                    ->waitForText('Updated Student Name',3)
                    ->assertSee('Updated Student Name'); // Ensure the updated student details are visible
        });
    }

    #[Test]
    public function a_user_can_delete_their_own_student()
    {
        $user = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $user->id]);

        $this->browse(function (Browser $browser) use ($user, $student) {
            $browser->loginAs($user)
                    ->visit('/students')
                    ->waitFor('.deleteButton',3)
                    ->click('.deleteButton[data-id="' . $student->id . '"]') // Use class and data attribute for delete button
                    ->acceptDialog() // Ensure you handle confirmation dialogs
                    ->waitFor("#successText",2)
                    ->assertSee('Student deleted successfully')
                    ->waitUntilMissingText($student->name,3)
                    ->assertDontSee($student->name); // Ensure the student is no longer in the list
        });
    }

    #[Test]
    public function a_user_cannot_update_or_delete_other_users_students()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $student = Student::factory()->create(['user_id' => $otherUser->id]);

        $this->browse(function (Browser $browser) use ($user, $student) {
            $browser->loginAs($user)
                    ->visit('/students')
                    ->assertDontSee($student->name);
        });
    }
}
