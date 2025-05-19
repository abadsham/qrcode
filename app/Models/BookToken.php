<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookToken extends Model
{
    use HasFactory;

    protected $fillable = ['book_id', 'token', 'used']; 

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user_book()
    {
        return $this->hasOne(UserBook::class);
    }
}
