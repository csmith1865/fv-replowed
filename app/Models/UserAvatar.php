<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAvatar extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'useravatars';

    protected $fillable = [
        'uid', 'value'
    ];

    // Assuming 'uid' is the foreign key for the user ID from the registration

    /**
     * Get the user associated with the userAvatar.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
