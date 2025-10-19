<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $guarded = [];
    protected $casts = ['is_delivery' => 'boolean'];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function replied()
    {
        return $this->belongsTo(Message::class, 'replied_by', 'identifier');
    }
}
