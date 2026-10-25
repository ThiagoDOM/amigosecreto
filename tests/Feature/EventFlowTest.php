<?php

namespace Tests\Feature;

use App\Mail\DrawNotificationMail;
use App\Models\Event;
use App\Models\MarketingSubscriber;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EventFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('og:title', false);
        $response->assertSee('og:image', false);
        $response->assertSee('og-home.jpg', false);
    }

    public function test_organizer_can_create_event(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/events', [
            'title' => 'Amigo Secreto da Família 2026',
            'budget_limit' => 60.00,
            'draw_date' => '2026-12-20',
            'event_date' => '2026-12-24',
            'location' => 'Casa da Vovó',
            'description' => 'Traga 1 prato salgado e sua bebida favorita!',
        ]);

        $this->assertDatabaseHas('events', [
            'user_id' => $user->id,
            'title' => 'Amigo Secreto da Família 2026',
            'budget_limit' => 60.00,
            'status' => 'open',
        ]);

        $event = Event::where('title', 'Amigo Secreto da Família 2026')->first();
        $this->assertNotNull($event->code);
        $response->assertRedirect(route('events.show', $event));
    }

    public function test_guest_can_view_invite_page_and_join(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Confraternização da Turma',
            'code' => Event::generateUniqueCode(),
            'budget_limit' => 50.00,
        ]);

        // View invite page
        $viewResponse = $this->get(route('invite.show', $event->code));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('og:title', false);
        $viewResponse->assertSee('Convite: Confraternização da Turma', false);
        $viewResponse->assertSee('og:image', false);
        $viewResponse->assertSee('og-invite.jpg', false);

        // Join event
        $joinResponse = $this->post(route('invite.join', $event->code), [
            'name' => 'Mariana Souza',
            'phone' => '(11) 99876-5432',
            'wishlist' => 'Livro de receitas e chocolates',
            'clothing_size' => 'M',
            'shoe_size' => '37',
        ]);

        $this->assertDatabaseHas('participants', [
            'event_id' => $event->id,
            'name' => 'Mariana Souza',
            'phone' => '(11) 99876-5432',
            'wishlist' => 'Livro de receitas e chocolates',
        ]);

        $participant = Participant::where('name', 'Mariana Souza')->first();
        $this->assertNotNull($participant->token);

        $joinResponse->assertRedirect(route('secret.show', ['token' => $participant->token]));
        $joinResponse->assertSessionHas('just_registered', true);
    }

    public function test_organizer_can_add_participant_manually_and_configure_exclusion(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Natal dos Amigos',
            'code' => Event::generateUniqueCode(),
        ]);

        // Manual add
        $this->actingAs($user)->post(route('events.participants.store', $event->id), [
            'name' => 'Tio Beto',
            'phone' => '(21) 98888-7777',
            'wishlist' => 'Vinho tinto seco',
        ]);

        $this->assertDatabaseHas('participants', [
            'event_id' => $event->id,
            'name' => 'Tio Beto',
        ]);

        $pA = Participant::where('name', 'Tio Beto')->first();
        $pB = Participant::create([
            'event_id' => $event->id,
            'name' => 'Tia Sandra',
            'token' => Participant::generateUniqueToken(),
        ]);

        // Add mutual exclusion
        $this->actingAs($user)->post(route('events.exclusions.store', $event->id), [
            'participant_id' => $pA->id,
            'excluded_participant_id' => $pB->id,
            'bidirectional' => true,
        ]);

        $this->assertDatabaseHas('participant_exclusions', [
            'event_id' => $event->id,
            'participant_id' => $pA->id,
            'excluded_participant_id' => $pB->id,
        ]);

        $this->assertDatabaseHas('participant_exclusions', [
            'event_id' => $event->id,
            'participant_id' => $pB->id,
            'excluded_participant_id' => $pA->id,
        ]);
    }

    public function test_full_draw_and_secret_reveal_flow(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Sorteio da Diretoria',
            'code' => Event::generateUniqueCode(),
        ]);

        $p1 = Participant::create(['event_id' => $event->id, 'name' => 'Lucas', 'token' => Participant::generateUniqueToken()]);
        $p2 = Participant::create(['event_id' => $event->id, 'name' => 'Julia', 'token' => Participant::generateUniqueToken()]);
        $p3 = Participant::create(['event_id' => $event->id, 'name' => 'Mateus', 'token' => Participant::generateUniqueToken()]);

        // Perform draw
        $drawResponse = $this->actingAs($user)->post(route('events.draw', $event->id));
        $drawResponse->assertSessionHas('success');

        $event->refresh();
        $this->assertTrue($event->isDrawn());

        $p1->refresh();
        $this->assertNotNull($p1->drawn_participant_id);
        $this->assertFalse($p1->isRevealed());

        // Participant opens magic link
        $revealPageResponse = $this->get(route('secret.show', $p1->token));
        $revealPageResponse->assertStatus(200);

        // Participant triggers reveal action
        $this->post(route('secret.reveal', $p1->token));
        $p1->refresh();
        $this->assertTrue($p1->isRevealed());
        $this->assertNotNull($p1->revealed_at);

        // Participant updates own wishlist
        $this->put(route('secret.wishlist.update', $p1->token), [
            'wishlist' => 'Fone de ouvido bluetooth',
            'clothing_size' => 'G',
            'shoe_size' => '42',
        ]);

        $p1->refresh();
        $this->assertEquals('Fone de ouvido bluetooth', $p1->wishlist);

        // Organizer resets draw
        $resetResponse = $this->actingAs($user)->post(route('events.reset-draw', $event->id));
        $resetResponse->assertSessionHas('success');

        $event->refresh();
        $this->assertFalse($event->isDrawn());

        $p1->refresh();
        $this->assertNull($p1->drawn_participant_id);
    }

    public function test_participant_can_opt_into_marketing_emails_and_handles_duplicates_safely(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Amigo da Firma',
            'code' => Event::generateUniqueCode(),
        ]);

        // First join with marketing opt-in
        $response1 = $this->post(route('invite.join', $event->code), [
            'name' => 'Felipe Ramos',
            'email' => 'felipe@example.com',
            'phone' => '(11) 97777-6666',
            'marketing_opt_in' => true,
        ]);

        $this->assertDatabaseHas('marketing_subscribers', [
            'email' => 'felipe@example.com',
            'name' => 'Felipe Ramos',
            'source' => 'participant_invite',
        ]);

        // Second event join with same email (should not fail or crash)
        $event2 = Event::create([
            'user_id' => $user->id,
            'title' => 'Amigo da Família',
            'code' => Event::generateUniqueCode(),
        ]);

        $response2 = $this->post(route('invite.join', $event2->code), [
            'name' => 'Felipe Ramos',
            'email' => 'felipe@example.com',
            'marketing_opt_in' => true,
        ]);

        $response2->assertSessionHas('success');
        $this->assertEquals(1, MarketingSubscriber::where('email', 'felipe@example.com')->count());
    }

    public function test_draw_sends_email_notification_to_participants_with_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Natal Incrível 2026',
            'code' => Event::generateUniqueCode(),
        ]);

        $p1 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'token' => Participant::generateUniqueToken(),
        ]);
        $p2 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Bruno',
            'email' => 'bruno@example.com',
            'token' => Participant::generateUniqueToken(),
        ]);
        $p3 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Carla Sem Email',
            'email' => null,
            'token' => Participant::generateUniqueToken(),
        ]);

        $response = $this->actingAs($user)->post(route('events.draw', $event->id));
        $response->assertSessionHas('success');

        // Verify only Alice and Bruno received email notifications
        Mail::assertSent(DrawNotificationMail::class, 2);

        Mail::assertSent(DrawNotificationMail::class, function (DrawNotificationMail $mail) use ($p1, $event): bool {
            return $mail->hasTo('alice@example.com')
                && $mail->participant->id === $p1->id
                && $mail->event->id === $event->id;
        });

        Mail::assertSent(DrawNotificationMail::class, function (DrawNotificationMail $mail) use ($p2, $event): bool {
            return $mail->hasTo('bruno@example.com')
                && $mail->participant->id === $p2->id
                && $mail->event->id === $event->id;
        });

        // Test mailable content rendering
        $mailable = new DrawNotificationMail($p1, $event);
        $mailable->assertSeeInHtml('Natal Incrível 2026');
        $mailable->assertSeeInHtml($p1->name);
        $mailable->assertSeeInHtml(route('secret.show', ['token' => $p1->token]));
    }
}
