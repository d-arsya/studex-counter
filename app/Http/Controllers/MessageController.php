<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function today()
    {
        $messages = Message::with(['contact', 'group', 'replied.contact'])->whereLike('time', now()->toDateString() . '%')->whereNotNull('replied_by')->get();

        $counts = collect();

        // Loop through messages and count the replied contacts
        foreach ($messages as $message) {
            $contact = $message->replied->contact;

            if ($contact) {
                $counts->put(
                    $contact->name,
                    ($counts->get($contact->name, 0) + 1)
                );
            }
        }
        return response()->json(['messages' => $messages, 'count' => $counts]);
    }

    public function weekly()
    {
        // Get the start and end of the current week (Sunday to Saturday)
        $startOfWeek = now()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $endOfWeek = now()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        $messages = Message::with(['contact', 'group', 'replied.contact'])
            ->whereBetween('time', [$startOfWeek, $endOfWeek])
            ->whereNotNull('replied_by')
            ->get();

        $counts = collect();

        // Loop through messages and count the replied contacts
        foreach ($messages as $message) {
            $contact = $message->replied->contact;

            if ($contact) {
                $counts->put(
                    $contact->name,
                    ($counts->get($contact->name, 0) + 1)
                );
            }
        }

        return response()->json([
            'messages' => $messages,
            'count' => $counts
        ]);
    }
}
