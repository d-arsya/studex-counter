<?php

namespace App\Http\Controllers;

use App\Models\Message;
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
}
