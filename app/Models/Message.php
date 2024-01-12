<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'comment',
        'user_id',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function highlight(): HasOne
    {
        $favourite = $this->hasOne(Comment::class, 'message_id', 'id')->where('favourite', '=', 1);

        if ($favourite->exists()) {
            return $favourite;
        } else {
            return $this->hasOne(Comment::class, 'message_id', 'id')->oldest();
        }
    }
}
