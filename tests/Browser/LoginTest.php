<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[Test]
    public function a_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', $user->email)
                    ->type('password', 'password123')
                    ->click("#loginbtn")
                    ->assertPathIs('/dashboard') 
                    ->assertSee('Dashboard'); 
        });
    }
}

