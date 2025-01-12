<?php

namespace App\Http\Controllers;

use App\Models\BandMember;
use App\Models\Name;
use App\Models\Vote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VotingController extends Controller
{
    /**
     * Wyświetla stronę głosowania dla danego członka zespołu.
     *
     * @param string $uuid
     * @return \Inertia\Response
     */
    public function index($uuid)
    {
        // Znajdź członka zespołu po UUID
        $bandMember = BandMember::where('uuid', $uuid)->firstOrFail();

        // Pobierz losową nazwę, na którą członek jeszcze nie głosował
        $name = Name::whereDoesntHave('votes', function ($query) use ($bandMember) {
            $query->where('band_member_id', $bandMember->id);
        })->inRandomOrder()->first();

        // Pobierz statystyki nazw
        $nameStatistics = Name::withCount([
            'votes as yes_votes' => function ($query) {
                $query->where('vote', true);
            },
            'votes as no_votes' => function ($query) {
                $query->where('vote', false);
            }
        ])
        ->orderByDesc('yes_votes')
        ->orderBy('no_votes')
        ->get();

        return Inertia::render('VotingPage', [
            'name' => $name,
            'bandMember' => $bandMember,
            'nameStatistics' => $nameStatistics,
        ]);
    }

    /**
     * Obsługuje oddanie głosu przez członka zespołu.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function vote(Request $request, $uuid)
    {
        // Znajdź członka zespołu
        $bandMember = BandMember::where('uuid', $uuid)->firstOrFail();

        // Walidacja danych
        $request->validate([
            'name_id' => 'required|exists:names,id',
            'vote' => 'required|boolean',
        ]);

        // Sprawdź, czy członek już głosował na tę nazwę
        $existingVote = Vote::where('band_member_id', $bandMember->id)
                            ->where('name_id', $request->name_id)
                            ->first();

        if ($existingVote) {
            return redirect()->route('voting.index', $uuid)->with('error', 'Już głosowałeś na tę nazwę.');
        }

        // Zapisz głos
        Vote::create([
            'band_member_id' => $bandMember->id,
            'name_id' => $request->name_id,
            'vote' => $request->vote,
        ]);

        return back()->with('success', 'Twój głos został zapisany.');
    }

    /**
     * Obsługuje dodanie nowych propozycji nazw przez członka zespołu.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $uuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addNames(Request $request, $uuid)
    {
        // Znajdź członka zespołu
        $bandMember = BandMember::where('uuid', $uuid)->firstOrFail();

        // Walidacja danych
        $request->validate([
            'names' => 'required|array',
            'names.*' => 'required|string|max:255',
        ]);

        foreach ($request->names as $name) {
            // Sprawdź, czy nazwa już istnieje
            if (!Name::where('name', $name)->exists()) {
                Name::create([
                    'name' => $name,
                    'band_member_id' => $bandMember->id,
                ]);
            }
        }

        return redirect()->route('voting.index', $uuid)->with('success', 'Nowe propozycje zostały dodane.');
    }
}
