<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class UserBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_token_id', 'buyer_name', 'buyer_email', 'buyer_phone', 'secure_id'
    ];
    protected static function booted()
    {
        static::creating(function ($userBook) {
            $userBook->secure_id = Str::uuid();
        });
    }

    public function bookToken()
    {
        return $this->belongsTo(BookToken::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
}
