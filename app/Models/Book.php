<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title'];
    
    protected static function booted()
    {
        static::creating(function ($book) {
            if (! $book->uuid) {
                $book->uuid = (string) Str::uuid();
            }
        });
    }
    
    public function book_token()
    {
        return $this->hasMany(BookToken::class);
    }
}
