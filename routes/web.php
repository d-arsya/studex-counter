<?php

use App\Models\Contact;
use App\Models\Group;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json('OK');
});
Route::post('webhook', function (Request $request) {
    $data = $request->data;
    try {
        $inserted = DB::table('datas')->create(['webhookdata' => json_encode($data)]);
        $da = json_decode($data);
        $groups = Group::where('is_cust', true)->pluck('identifier')->toArray();
        if (in_array($da->from, $groups)) {
            $exist = Contact::where('identifier', $da->author)->first();
            if (!$exist) {
                Contact::create(['name' => $da->_data->notifyName, 'identifier' => $da->author]);
            }
            $sw[] = $da;
            $contact = Contact::where('identifier', $da->author)->first()->id;
            $group = Group::where('identifier', $da->from)->first()->id;
            $exist = Message::where('identifier', $da->id->id)->first();
            if (!$exist) {
                Message::create([
                    'group_id' => $group,
                    'contact_id' => $contact,
                    'text' => $da->body,
                    'time' => Carbon::createFromTimestamp($da->timestamp)->addHours(7),
                    'identifier' => $da->id->id
                ]);
            }
            if ($da->hasQuotedMsg) {
                Message::where('identifier', $da->_data->quotedStanzaID)->update(['replied_by' => $da->id->id]);
            }
            $inserted->delete();
        }
        return response()->json(["success" => true]);
    } catch (\Throwable $th) {
        return response()->json(["success" => false, "message" => $th->getMessage()]);
    }
})->withoutMiddleware(VerifyCsrfToken::class);

// Route::get('coba', function () {
//     $data = DB::table('datas')->latest()->limit(100)->pluck('webhookdata');
//     $groups = Group::where('is_cust', true)->pluck('identifier')->toArray();
//     $sw = [];
//     foreach ($data as $value) {
//         $da = json_decode($value);
//         if (in_array($da->from, $groups)) {
//             $exist = Contact::where('identifier', $da->author)->first();
//             if (!$exist) {
//                 Contact::create(['name' => $da->_data->notifyName, 'identifier' => $da->author]);
//             }
//             $sw[] = $da;
//             $contact = Contact::where('identifier', $da->author)->first()->id;
//             $group = Group::where('identifier', $da->from)->first()->id;
//             $exist = Message::where('identifier', $da->id->id)->first();
//             if (!$exist) {
//                 Message::create([
//                     'group_id' => $group,
//                     'contact_id' => $contact,
//                     'text' => $da->body,
//                     'time' => Carbon::createFromTimestamp($da->timestamp)->addHours(7),
//                     'identifier' => $da->id->id
//                 ]);
//             }
//             if ($da->hasQuotedMsg) {
//                 Message::where('identifier', $da->_data->quotedStanzaID)->update(['replied_by' => $da->id->id]);
//             }
//         }
//     }

//     return response()->json($sw);
// });
