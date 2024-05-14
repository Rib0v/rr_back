<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class Advert extends Model
{
    use HasFactory;

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    /** @param  Builder|\Illuminate\Database\Query\Builder $query */
    public function scopeSort(Builder $query, Request $request)
    {
        // По дефолту показываем сначала самые свежие
        $sortBy = $request->query('sort', 'newer');

        switch ($sortBy) {
            case 'lowprice':
                $query->orderBy('price');
                break;
            case 'hiprice':
                $query->orderByDesc('price');
                break;
            case 'older':
                $query->oldest();
                break;
            case 'newer':
                $query->latest();
                break;
        }
    }
}
