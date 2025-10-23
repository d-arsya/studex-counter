<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\Group;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DecodeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'decode:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = DB::table('datas')->latest()->pluck('webhookdata');
        $groups = Group::where('is_cust', true)->pluck('identifier')->toArray();
        $success = true;
        foreach ($data as $value) {
            $da = json_decode($value);
            if (in_array($da->from, $groups)) {
                $exist = Contact::where('identifier', $da->author)->first();
                if (!$exist) {
                    Contact::create(['name' => $da->_data->notifyName, 'identifier' => $da->author]);
                }
                $contact = Contact::where('identifier', $da->author)->first()->id;
                $group = Group::where('identifier', $da->from)->first()->id;
                $exist = Message::where('identifier', $da->id->id)->first();
                try {
                    if (!$exist) {
                        Message::create([
                            'group_id' => $group,
                            'contact_id' => $contact,
                            'text' => $da->body,
                            'time' => Carbon::createFromTimestamp($da->timestamp)->addHours(7),
                            'identifier' => $da->id->id,
                            'is_delivery' => str_contains(strtolower($da->body), 'jastip')
                        ]);
                    }
                    if ($da->hasQuotedMsg) {
                        $quoted = Message::where('identifier', $da->_data->quotedStanzaID)->first();
                        if ($quoted) {
                            $quoted = $this->createMessage($da);
                        }
                        if ($quoted->replied_by == null && strlen($da->body) < 6 && !str_contains(strtolower($da->body), 'm')) {
                            $quoted->update(['replied_by' => $da->id->id]);
                        }
                    }
                } catch (\Throwable $th) {
                    if ($success) {
                        $success = false;
                    }
                    logs()->error($th->getMessage());
                    logs()->info(json_encode($da));
                    break;
                }
            }
        }
        if ($success) {
            DB::table('datas')->delete();
        }
        return 0;
    }

    private function createMessage($da)
    {
        $contact = Contact::where('identifier', $da->_data->quotedParticipant)->first()->id;

        $group = Group::where('identifier', $da->from)->first()->id;
        $data = Message::create([
            'group_id' => $group,
            'contact_id' => $contact,
            'text' => $da->body,
            'time' => Carbon::createFromTimestamp($da->timestamp)->addHours(7),
            'identifier' => $da->id->id,
            'is_delivery' => str_contains(strtolower($da->body), 'jastip')
        ]);
        return $data;
    }
}
