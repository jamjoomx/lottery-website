<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Draw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Get all tickets for authenticated user.
     */
    public function index()
    {
        $tickets = Ticket::with('draw')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return response()->json($tickets);
    }

    /**
     * Purchase a new lottery ticket.
     */
    public function buy(Request $request)
    {
        $request->validate([
            'numbers' => 'required|array|size:6',
            'numbers.*' => 'integer|min:1|max:49|distinct',
        ]);

        $draw = Draw::where('status', 'open')->latest()->first();
        if (!$draw) {
            return response()->json(['message' => 'No open draw available.'], 404);
        }

        $user = Auth::user();
        $ticketPrice = 10.00;

        if ($user->balance < $ticketPrice) {
            return response()->json(['message' => 'Insufficient balance.'], 422);
        }

        // Deduct balance
        $user->decrement('balance', $ticketPrice);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'draw_id' => $draw->id,
            'numbers' => json_encode(sort($request->numbers) ? $request->numbers : $request->numbers),
            'price'   => $ticketPrice,
        ]);

        return response()->json([
            'message' => 'Ticket purchased successfully!',
            'ticket'  => $ticket,
        ], 201);
    }

    /**
     * Check if a ticket is a winner.
     */
    public function checkResult($id)
    {
        $ticket = Ticket::with(['draw', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json([
            'ticket'          => $ticket,
            'your_numbers'    => $ticket->numbers,
            'winning_numbers' => $ticket->draw->winning_numbers,
            'status'          => $ticket->status,
        ]);
    }
}
