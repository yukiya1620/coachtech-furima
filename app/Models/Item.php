<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'price',
        'condition',
        'description',
        'image_path',
        'is_sold',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function categories() {
        return $this->belongsToMany(Category::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function purchase() {
        return $this->hasOne(Purchase::class);
    }

    public function isLikedBy(?\App\Models\User $user): bool {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }
    
    public function getImageUrlAttribute(): string {
        $path = $this->image_path;
        
        if (!$path) {
            return asset('images/no-image.png');
        }
        
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        
        if (Str::startsWith($path, '/storage/')) {
            return $path;
        }
        
        if (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        }
        
        return Storage::url($path);
    }

}
