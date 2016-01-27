<?php

namespace App\Models;

class UserAvailability extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'user_availability';

    /**
     * {inheritdoc}
     */
    public $timestamps = false;

    /**
     * Availability is assigned to specific user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
