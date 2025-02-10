<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWorld extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'userworlds';

    protected $fillable = [
        'uid', "type", 'sizeX', 'sizeY', 'objects', 'messageManager'
    ];

    // Assuming 'uid' is the foreign key for the user ID from the registration

    /**
     * Get the user associated with the UserMeta.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
