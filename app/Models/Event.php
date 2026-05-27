<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location_text',
        'lat',
        'lng',
        'event_date',
        'event_time',
        'max_participants',
        'is_active',
    ];

    protected $casts = [
        'event_date' => 'date',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'max_participants' => 'integer',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')
            ->withTimestamps();
    }

    public function statusBadge(): array
    {
        if ($this->is_active === false) {
            return [
                'key' => 'cancelled',
                'label' => 'Cancelado',
            ];
        }

        $max = (int) ($this->max_participants ?? 0);
        $count = (int) ($this->participants_count ?? $this->participants()->count());

        if ($max > 0 && $count >= $max) {
            return [
                'key' => 'full',
                'label' => 'Cheio',
            ];
        }

        if ($this->isPast()) {
            return [
                'key' => 'past',
                'label' => 'Terminado',
            ];
        }

        return [
            'key' => 'active',
            'label' => 'Ativo',
        ];
    }

    public function canJoin(): bool
    {
        return $this->statusBadge()['key'] === 'active';
    }

    public function isPast(): bool
    {
        if (!$this->event_date) {
            return false;
        }

        $date = Carbon::parse($this->event_date)->toDateString();
        $time = $this->event_time ?: '23:59:59';

        return Carbon::parse($date . ' ' . $time)->isPast();
    }
}