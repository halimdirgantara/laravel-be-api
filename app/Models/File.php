<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'file', 'path', 'file_type', 'description', 'size', 'downloaded', 'user_id'
    ];

    public function categories() {
        return $this->hasMany(Category::class);
    }
}
