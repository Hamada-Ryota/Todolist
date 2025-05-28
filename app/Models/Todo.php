<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    protected $fillable = ['title', 'completed', 'due_date', 'priority'];

    public function todos()
    {
        return $this->belongsToMany(Todo::class);
    }
}
