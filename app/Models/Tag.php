<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function todos()
    {
        return $this->belongsToMany(Todo::class);
    }
}
