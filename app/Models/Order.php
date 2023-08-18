<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'user_id'];

    public function user(): BelongsTo {

        return $this->belongsTo(User::class);
    }

    public function books(): BelongsToMany {

        return $this->belongsToMany(Book::class)->withPivot(['quantity', 'unit_price']);
    }
}
