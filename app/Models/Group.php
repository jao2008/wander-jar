<?php

namespace App\Models;

use App\Models\GroupMessage;
use App\Models\Pin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'privacy',
        'map_style',
        'invite_code',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function pins(): HasMany
    {
        return $this->hasMany(Pin::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class)->latest();
    }
}