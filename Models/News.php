<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    protected $fillable = ['category_id', 'content', 'image'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CategoryNew::class);
    }

}
