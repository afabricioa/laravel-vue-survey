<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Survey extends Model
{
    use HasFactory, HasSlug;

    const TYPE_TEXT = 'text';
    const TYPE_SELECT = 'select';
    const TYPE_RADIO = 'radio';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_TEXTAREA = 'textarea';

    protected $fillable = ['user_id', 'title', 'slug', 'status', 'description', 'expire_date'];
    //fillable - fields that can be mass assigned

    public function getSlugOptions(): SlugOptions{
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}
