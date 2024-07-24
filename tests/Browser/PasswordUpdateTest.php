<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PasswordUpdateTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_update_their_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile') 
                    ->type('current_password', 'oldpassword123')
                    ->type('password', 'newpassword123')
                    ->type('password_confirmation', 'newpassword123')
                    ->click('#updatePasswordBtn')
                    ->assertPathIs('/profile') 
                    ->assertSee('Password updated.'); 
        });
    }

    /** @test */
    public function a_user_cannot_update_password_with_incorrect_current_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile') 
                    ->type('current_password', 'wrongpassword')
                    ->type('password', 'newpassword123')
                    ->type('password_confirmation', 'newpassword123')
                    ->click('#updatePasswordBtn')
                    ->assertPathIs('/profile') 
                    ->assertSee('The password is incorrect.'); 
        });
    }

    /** @test */
    public function a_user_cannot_update_password_with_mismatched_confirmation()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile') 
                    ->type('current_password', 'oldpassword123')
                    ->type('password', 'newpassword123')
                    ->type('password_confirmation', 'differentpassword123')
                    ->click('#updatePasswordBtn')
                    ->assertPathIs('/profile') 
                    ->assertSee('The password field confirmation does not match.'); 
        });
    }

}
