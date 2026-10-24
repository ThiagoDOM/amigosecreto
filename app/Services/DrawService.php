<?php

namespace App\Services;

use App\Models\Event;
use DomainException;
use Illuminate\Support\Facades\DB;

class DrawService
{
    /**
     * Perform the Secret Santa draw for an event.
     *
     * @throws DomainException
     */
    public function draw(Event $event): Event
    {
        $participants = $event->participants()->get();

        if ($participants->count() < 3) {
            throw new DomainException('É necessário ter pelo menos 3 participantes para realizar o sorteio.');
        }

        $participantIds = $participants->pluck('id')->all();
        $exclusions = $event->exclusions()->get();

        // Build exclusion lookup: [giver_id => [excluded_id_1, excluded_id_2]]
        $forbiddenMap = [];
        foreach ($participantIds as $id) {
            $forbiddenMap[$id] = [$id]; // Self is always forbidden
        }

        foreach ($exclusions as $exclusion) {
            $forbiddenMap[$exclusion->participant_id][] = $exclusion->excluded_participant_id;
        }

        // Try to find a single Hamiltonian cycle first for maximum fun
        $pairings = $this->findHamiltonianCycle($participantIds, $forbiddenMap);

        // If no single cycle exists due to strict restrictions, try general derangement
        if ($pairings === null) {
            $pairings = $this->findValidDerangement($participantIds, $forbiddenMap);
        }

        if ($pairings === null) {
            throw new DomainException(
                'Não foi possível encontrar uma combinação válida com as restrições atuais. '.
                'Por favor, revise ou remova algumas restrições entre participantes.'
            );
        }

        // Persist pairings in database transaction
        DB::transaction(function () use ($event, $pairings): void {
            foreach ($pairings as $giverId => $receiverId) {
                $event->participants()->where('id', $giverId)->update([
                    'drawn_participant_id' => $receiverId,
                    'revealed_at' => null,
                ]);
            }

            $event->update([
                'status' => 'drawn',
                'drawn_at' => now(),
            ]);
        });

        return $event->fresh(['participants.drawnParticipant', 'exclusions']);
    }

    /**
     * Reset the draw for an event back to open status.
     */
    public function reset(Event $event): Event
    {
        DB::transaction(function () use ($event): void {
            $event->participants()->update([
                'drawn_participant_id' => null,
                'revealed_at' => null,
            ]);

            $event->update([
                'status' => 'open',
                'drawn_at' => null,
            ]);
        });

        return $event->fresh(['participants', 'exclusions']);
    }

    /**
     * Attempt to find a single closed loop (Hamiltonian cycle) covering all participants.
     *
     * @param  list<int>  $ids
     * @param  array<int, list<int>>  $forbidden
     * @return array<int, int>|null [giver_id => receiver_id]
     */
    protected function findHamiltonianCycle(array $ids, array $forbidden, int $maxAttempts = 1500): ?array
    {
        $n = count($ids);

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $shuffled = $ids;
            shuffle($shuffled);

            $valid = true;
            $pairings = [];

            for ($i = 0; $i < $n; $i++) {
                $giver = $shuffled[$i];
                $receiver = $shuffled[($i + 1) % $n];

                if (in_array($receiver, $forbidden[$giver], true)) {
                    $valid = false;
                    break;
                }

                $pairings[$giver] = $receiver;
            }

            if ($valid) {
                return $pairings;
            }
        }

        return null;
    }

    /**
     * Backtracking randomized derangement algorithm.
     *
     * @param  list<int>  $ids
     * @param  array<int, list<int>>  $forbidden
     * @return array<int, int>|null
     */
    protected function findValidDerangement(array $ids, array $forbidden, int $maxAttempts = 2000): ?array
    {
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $givers = $ids;
            shuffle($givers);

            $availableReceivers = $ids;
            $pairings = [];
            $failed = false;

            foreach ($givers as $giver) {
                // Allowed receivers for this giver that are still available
                $candidates = array_values(array_filter(
                    $availableReceivers,
                    fn ($r): bool => ! in_array($r, $forbidden[$giver], true)
                ));

                if (empty($candidates)) {
                    $failed = true;
                    break;
                }

                // Pick random candidate
                $pickedReceiver = $candidates[array_rand($candidates)];
                $pairings[$giver] = $pickedReceiver;

                // Remove picked receiver from available
                $key = array_search($pickedReceiver, $availableReceivers, true);
                unset($availableReceivers[$key]);
                $availableReceivers = array_values($availableReceivers);
            }

            if (! $failed && count($pairings) === count($ids)) {
                return $pairings;
            }
        }

        return null;
    }
}
