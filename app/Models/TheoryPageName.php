<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoryPageName extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    public function pages()
    {
        return $this->hasMany(TheoryPage::class);
    }
}
