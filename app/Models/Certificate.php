<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = ['user_book_id', 'certificate_number'];

    public function user_book()
    {
        return $this->belongsTo(UserBook::class);
    }
}
