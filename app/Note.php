<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /**
     * Fields inside this property can be assigned using Eloquent's methods:
     *   - create()
     *   - update()
     */
    protected $fillable = ['author', 'title', 'body', 'created_at'];
}
