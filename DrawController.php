<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Ticket;
use App\Models\Winner;
use Illuminate\Http\Request;

class DrawController extends Controller
{
    /**
     * Get latest draw result (public).
     */
    public function latest()
    {
        $draw = Draw::where('status', 'drawn')->latest('drawn_at')->first();
        return response()->json($draw);
    }

    /**
     * Admin: list all draws.
     */
    public function index()
    {
        return response()->json(Draw::orderByDesc('created_at')->get());
    }

    /**
     * Admin: create a new open draw.
     */
    public function create()
    {
        $draw = Draw::create(['status' => 'open']);
        return response()->json(['message' => 'Draw created.', 'draw' => $draw], 201);
    }

    /**
     * Admin: execute the draw — picks 6 random winning numbers.
     */
    public function execute($id)
    {
        $draw = Draw::findOrFail($id);

        if ($draw->status !== 'open') {
            return response()->json(['message' => 'Draw is not open.'], 422);
        }

        // Generate 6 unique winning numbers between 1-49
        $numbers = [];
        while (count($numbers) < 6) {
            $n = random_int(1, 49);
            if (!in_array($n, $numbers)) {
                $numbers[] = $n;
            }
        }
        sort($numbers);

        $draw->update([
            'winning_numbers' => json_encode($numbers),
            'status'          => 'drawn',
            'drawn_at'        => now(),
        ]);

        // Evaluate all tickets for this draw
        $tickets = Ticket::where('draw_id', $draw->id)->get();
        foreach ($tickets as $ticket) {
            $ticketNums  = json_decode($ticket->numbers, true);
            $matches     = count(array_intersect($ticketNums, $numbers));
            $prize       = $this->calculatePrize($matches);

            if ($prize > 0) {
                $ticket->update(['status' => 'won']);
                Winner::create([
                    'ticket_id'    => $ticket->id,
                    'user_id'      => $ticket->user_id,
                    'draw_id'      => $draw->id,
                    'prize_amount' => $prize,
                ]);
                $ticket->user->increment('balance', $prize);
            } else {
                $ticket->update(['status' => 'lost']);
            }
        }

        return response()->json(['message' => 'Draw executed.', 'winning_numbers' => $numbers]);
    }

    private function calculatePrize(int $matches): float
    {
        return match ($matches) {
            6 => 100000.00,
            5 => 5000.00,
            4 => 500.00,
            3 => 50.00,
            default => 0.00,
        };
    }
}
