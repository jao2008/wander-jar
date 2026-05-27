<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Pin extends Model
{
    protected $fillable = [
        'user_id',
        'group_id',
        'title',
        'content',
        'lat',
        'lng',
        'location_text',
        'image_path',
    ];

    protected $appends = [
        'image_url',
        'image_storage_path',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): ?string
    {
        $path = $this->image_storage_path;

        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        /*
         | Só devolve URL se o ficheiro existir mesmo no disco public.
         | Assim evita mostrar imagens quebradas.
         */
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function getImageStoragePathAttribute(): ?string
    {
        $path = $this->image_path;

        if (!$path) {
            return null;
        }

        $path = trim((string) $path);
        $path = str_replace('\\', '/', $path);

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, '/storage/')) {
            return ltrim(Str::after($path, '/storage/'), '/');
        }

        if (Str::startsWith($path, 'storage/')) {
            return ltrim(Str::after($path, 'storage/'), '/');
        }

        if (Str::startsWith($path, 'public/')) {
            return ltrim(Str::after($path, 'public/'), '/');
        }

        return ltrim($path, '/');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function belongsToCurrentUser(): bool
    {
        return auth()->check() && $this->user_id === auth()->id();
    }
}