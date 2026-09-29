<?php

namespace App\Models;

use App\Enums\PrizeTier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrawPrize extends Model
{
    public $timestamps = false;

    protected $fillable = ['draw_id', 'tier', 'number', 'amount'];

    protected function casts(): array
    {
        return [
            'tier' => PrizeTier::class,
            'amount' => 'integer',
        ];
    }

    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }
}
