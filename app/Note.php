<?php

namespace App;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /**
     * Indicates if the model should be timestamped.
     * By default laravel expects created_at and updated_at columns to exists
     * in the database table, uncomment the next line to disable this behaviour.
     */
    // public $timestamps = false;

    /**
     * Fields inside this property can be assigned using Eloquent methods:
     *   - create()
     *   - update()
     */
    protected $fillable = ['author', 'title', 'body'];

    // Hide fields from array or JSON representation of this model.
    protected $hidden = ['id'];

    /**
     * The attributes that should be mutated to dates.
     */
    protected $dates = ['updated_at'];

    /**
     * Setup model event hooks
     * This method will be called when a note is created and it adds the uuid.
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4();
        });
    }
}
