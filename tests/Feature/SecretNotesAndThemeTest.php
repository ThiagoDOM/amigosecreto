<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Participant;
use App\Models\SecretMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecretNotesAndThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_can_be_created_with_custom_theme(): void
    {
        $organizer = User::factory()->create();

        $response = $this->actingAs($organizer)->post('/events', [
            'title' => 'Amigo Secreto da Empresa',
            'theme' => 'chocolate',
            'budget_limit' => 50.00,
        ]);

        $this->assertDatabaseHas('events', [
            'title' => 'Amigo Secreto da Empresa',
            'theme' => 'chocolate',
        ]);
    }

    public function test_participant_cannot_send_secret_question_before_event_draw(): void
    {
        $organizer = User::factory()->create();
        $event = Event::create([
            'user_id' => $organizer->id,
            'title' => 'Amigo Caneca 2026',
            'code' => Event::generateUniqueCode(),
            'status' => 'open',
            'theme' => 'mug',
        ]);

        $participant = Participant::create([
            'event_id' => $event->id,
            'name' => 'Participante Um',
            'token' => Participant::generateUniqueToken(),
        ]);

        $response = $this->post(route('secret.ask', $participant->token), [
            'question' => 'Qual a sua cor de caneca preferida?',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('secret_messages', 0);
    }

    public function test_participant_can_send_anonymous_secret_question_after_draw(): void
    {
        $organizer = User::factory()->create();
        $event = Event::create([
            'user_id' => $organizer->id,
            'title' => 'Amigo Livro',
            'code' => Event::generateUniqueCode(),
            'status' => 'drawn',
            'drawn_at' => now(),
            'theme' => 'book',
        ]);

        $p1 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Participante A',
            'token' => Participant::generateUniqueToken(),
        ]);

        $p2 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Participante B',
            'token' => Participant::generateUniqueToken(),
        ]);

        // P1 drew P2
        $p1->update(['drawn_participant_id' => $p2->id]);
        $p2->update(['drawn_participant_id' => $p1->id]);

        $response = $this->post(route('secret.ask', $p1->token), [
            'question' => 'Qual gênero de livro você mais gosta de ler?',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('secret_messages', [
            'event_id' => $event->id,
            'sender_participant_id' => $p1->id,
            'recipient_participant_id' => $p2->id,
            'question' => 'Qual gênero de livro você mais gosta de ler?',
            'answer' => null,
        ]);
    }

    public function test_recipient_can_answer_secret_question_anonymously(): void
    {
        $organizer = User::factory()->create();
        $event = Event::create([
            'user_id' => $organizer->id,
            'title' => 'Amigo Secreto Tradicional',
            'code' => Event::generateUniqueCode(),
            'status' => 'drawn',
            'drawn_at' => now(),
            'theme' => 'traditional',
        ]);

        $p1 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Participante Alpha',
            'token' => Participant::generateUniqueToken(),
        ]);

        $p2 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Participante Beta',
            'token' => Participant::generateUniqueToken(),
        ]);

        $p1->update(['drawn_participant_id' => $p2->id]);
        $p2->update(['drawn_participant_id' => $p1->id]);

        $message = SecretMessage::create([
            'event_id' => $event->id,
            'sender_participant_id' => $p1->id,
            'recipient_participant_id' => $p2->id,
            'question' => 'Você prefere camisa gola polo ou básica?',
        ]);

        // P2 (recipient) answers the question
        $response = $this->post(route('secret.answer', [$p2->token, $message->id]), [
            'answer' => 'Prefiro básica, cor preta ou azul marinho!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('secret_messages', [
            'id' => $message->id,
            'answer' => 'Prefiro básica, cor preta ou azul marinho!',
        ]);

        $this->assertNotNull($message->fresh()->answered_at);
    }

    public function test_unrelated_participant_cannot_answer_others_secret_message(): void
    {
        $organizer = User::factory()->create();
        $event = Event::create([
            'user_id' => $organizer->id,
            'title' => 'Amigo Chinelo',
            'code' => Event::generateUniqueCode(),
            'status' => 'drawn',
            'drawn_at' => now(),
            'theme' => 'havaianas',
        ]);

        $p1 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Participante 1',
            'token' => Participant::generateUniqueToken(),
        ]);

        $p2 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Participante 2',
            'token' => Participant::generateUniqueToken(),
        ]);

        $p3 = Participant::create([
            'event_id' => $event->id,
            'name' => 'Participante 3 (Invasor)',
            'token' => Participant::generateUniqueToken(),
        ]);

        $message = SecretMessage::create([
            'event_id' => $event->id,
            'sender_participant_id' => $p1->id,
            'recipient_participant_id' => $p2->id,
            'question' => 'Qual tamanho de chinelo você calça?',
        ]);

        // P3 tries to answer P2's question
        $response = $this->post(route('secret.answer', [$p3->token, $message->id]), [
            'answer' => 'Tentando bisbilhotar',
        ]);

        $response->assertStatus(403);
        $this->assertNull($message->fresh()->answer);
    }
}
