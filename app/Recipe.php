<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Recipe extends Model
{
    protected $fillable = ['title','procedure'];

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id', 'id');
    }
}
