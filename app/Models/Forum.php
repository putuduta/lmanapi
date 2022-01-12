<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;
    protected $table = 'forums';
    protected $primaryKey = 'id';
    protected $timestamp = true;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function schedule()
    {
        return $this->belongsTo('App\Models\Schedule', 'schedule_id', 'id');
    }  

    public function forumReplies(){
        return $this->hasMany('App\Models\ForumReply', 'id','forum_id')->withDefault();
    } 
}
