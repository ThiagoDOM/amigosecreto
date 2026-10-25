<?php

namespace Tests\Feature\Auth;

use App\Models\MarketingSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_must_accept_terms_to_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => false,
        ]);

        $response->assertSessionHasErrors('terms');
        $this->assertGuest();
    }

    public function test_organizer_registration_subscribes_to_marketing_when_opted_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'Carla Organizadora',
            'email' => 'carla@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
            'marketing_opt_in' => true,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('marketing_subscribers', [
            'email' => 'carla@example.com',
            'name' => 'Carla Organizadora',
            'source' => 'organizer_registration',
        ]);
    }

    public function test_organizer_registration_with_existing_marketing_email_continues_normally(): void
    {
        MarketingSubscriber::create([
            'email' => 'carla@example.com',
            'name' => 'Carla Anterior',
            'source' => 'participant_invite',
        ]);

        $response = $this->post('/register', [
            'name' => 'Carla Organizadora',
            'email' => 'carla@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
            'marketing_opt_in' => true,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertEquals(1, MarketingSubscriber::where('email', 'carla@example.com')->count());
    }
}
