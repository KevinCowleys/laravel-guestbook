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

    /**
     * Gets the user linked to the message
     *
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Get the favourite comment
     *
     * @return HasOne
     */
    public function highlight(): HasOne
    {
        return $this->hasOne(Comment::class, 'message_id', 'id')->where('favourite', '=', 1);
    }

    /**
     * Get the oldest comment, ignoring the favourite one,
     * this is done for eager loading reasons
     *
     * @return HasOne
     */
    public function firstComment(): HasOne
    {
        return $this->hasOne(Comment::class, 'message_id', 'id')->where('favourite', '!=', 1)->oldest();
    }
}
