<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function genre(): BelongsTo {

        return $this->belongsTo(BookGenre::class);
    }

    public function getPriceAttribute(): Attribute {

        return $this->price * 100;
    }

    public function setPriceAttribute(): Attribute {

        return $this->price * 100;
    }
}
