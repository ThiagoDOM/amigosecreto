<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\Participant;
use App\Models\ParticipantExclusion;
use App\Models\User;
use App\Services\DrawService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DrawServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_draw_with_fewer_than_three_participants(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Natal da Família',
            'code' => Event::generateUniqueCode(),
        ]);

        Participant::create([
            'event_id' => $event->id,
            'name' => 'Ana',
            'token' => Participant::generateUniqueToken(),
        ]);

        Participant::create([
            'event_id' => $event->id,
            'name' => 'Bruno',
            'token' => Participant::generateUniqueToken(),
        ]);

        $service = new DrawService;

        $this->expectException(DomainException::class);
        $service->draw($event);
    }

    public function test_draw_assigns_valid_secret_santa_without_self_draw(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Amigo da Firma',
            'code' => Event::generateUniqueCode(),
        ]);

        $names = ['Carlos', 'Diana', 'Eduardo', 'Fernanda'];
        $participants = [];
        foreach ($names as $name) {
            $participants[] = Participant::create([
                'event_id' => $event->id,
                'name' => $name,
                'token' => Participant::generateUniqueToken(),
            ]);
        }

        $service = new DrawService;
        $updatedEvent = $service->draw($event);

        $this->assertTrue($updatedEvent->isDrawn());
        $this->assertEquals('drawn', $updatedEvent->status);
        $this->assertNotNull($updatedEvent->drawn_at);

        $givers = [];
        $receivers = [];

        foreach ($updatedEvent->participants as $p) {
            $this->assertNotNull($p->drawn_participant_id);
            // No one should draw themselves
            $this->assertNotEquals($p->id, $p->drawn_participant_id);

            $givers[] = $p->id;
            $receivers[] = $p->drawn_participant_id;
        }

        // Each person gives once and receives once
        $this->assertCount(4, array_unique($givers));
        $this->assertCount(4, array_unique($receivers));
    }

    public function test_draw_respects_exclusions(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Natal com Casais',
            'code' => Event::generateUniqueCode(),
        ]);

        // 4 participants: Couple 1 (A, B) cannot draw each other, Couple 2 (C, D) cannot draw each other
        $pA = Participant::create(['event_id' => $event->id, 'name' => 'Alice', 'token' => Participant::generateUniqueToken()]);
        $pB = Participant::create(['event_id' => $event->id, 'name' => 'Bob', 'token' => Participant::generateUniqueToken()]);
        $pC = Participant::create(['event_id' => $event->id, 'name' => 'Carol', 'token' => Participant::generateUniqueToken()]);
        $pD = Participant::create(['event_id' => $event->id, 'name' => 'David', 'token' => Participant::generateUniqueToken()]);

        // A cannot draw B, B cannot draw A
        ParticipantExclusion::create(['event_id' => $event->id, 'participant_id' => $pA->id, 'excluded_participant_id' => $pB->id]);
        ParticipantExclusion::create(['event_id' => $event->id, 'participant_id' => $pB->id, 'excluded_participant_id' => $pA->id]);

        // C cannot draw D, D cannot draw C
        ParticipantExclusion::create(['event_id' => $event->id, 'participant_id' => $pC->id, 'excluded_participant_id' => $pD->id]);
        ParticipantExclusion::create(['event_id' => $event->id, 'participant_id' => $pD->id, 'excluded_participant_id' => $pC->id]);

        $service = new DrawService;
        $service->draw($event);

        $pA->refresh();
        $pB->refresh();
        $pC->refresh();
        $pD->refresh();

        $this->assertNotEquals($pB->id, $pA->drawn_participant_id);
        $this->assertNotEquals($pA->id, $pB->drawn_participant_id);
        $this->assertNotEquals($pD->id, $pC->drawn_participant_id);
        $this->assertNotEquals($pC->id, $pD->drawn_participant_id);
    }

    public function test_reset_draw_clears_pairings_and_status(): void
    {
        $user = User::factory()->create();
        $event = Event::create([
            'user_id' => $user->id,
            'title' => 'Evento Teste',
            'code' => Event::generateUniqueCode(),
        ]);

        foreach (['Ana', 'Bia', 'Caio'] as $name) {
            Participant::create([
                'event_id' => $event->id,
                'name' => $name,
                'token' => Participant::generateUniqueToken(),
            ]);
        }

        $service = new DrawService;
        $service->draw($event);
        $event->refresh();
        $this->assertTrue($event->isDrawn());

        $service->reset($event);
        $event->refresh();

        $this->assertEquals('open', $event->status);
        $this->assertNull($event->drawn_at);

        foreach ($event->participants as $p) {
            $this->assertNull($p->drawn_participant_id);
        }
    }
}
