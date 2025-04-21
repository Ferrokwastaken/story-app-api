<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * This model represents a category within the application.
 * 
 * This model specifies all the fields that can be modified alongside all the
 * relationship instances associated with the 'categories' table, which includes
 * one-to-many relationships.
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        /**
         * The name of the category of the story
         * @var string
         */
        'name',
        /**
         * The genre in which the category defines itself
         * @var string
         */
        'genre',
    ];

    /**
     * Get the stories that belong to the category.
     * 
     * This method defines a one-to-many relationship between the Category and Story models.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stories() : HasMany {
        return $this->hasMany(Story::class);
    }
}
