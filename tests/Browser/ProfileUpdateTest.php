<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;

class ProfileUpdateTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function a_user_can_update_their_profile()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'oldemail@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->type('name', 'New Name')
                    ->type('email', 'newemail@example.com')
                    ->click('#updateProfileBtn')
                    ->assertPathIs('/profile') 
                    ->assertInputValue('name', 'New Name')
                    ->assertInputValue('email', 'newemail@example.com');
        });
    }

    #[Test]
    public function a_user_cannot_update_profile_with_existing_email()
    {

        $another_user = User::factory()->create([
            'name' => 'Ano Name',
            'email' => 'anemail@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'oldemail@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user,$another_user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->type('name', 'New Name')
                    ->type('email', $another_user->email)
                    ->click('#updateProfileBtn')
                    ->assertPathIs('/profile') 
                    ->assertSee('The email has already been taken.');
        });
    }

}
