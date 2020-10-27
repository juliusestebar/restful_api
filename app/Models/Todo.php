<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
    	'task',
    	'time',
    	'status',
    	'user_id'
    ];

    public function tasks(){
    	return $this->belongsTo(User::class);
    }
}
