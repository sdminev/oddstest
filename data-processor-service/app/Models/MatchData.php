<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchData extends Model
{
    protected $table = 'match_data';

    protected $fillable = [
        'match_id',
        'bookmaker',
        'home_team',
        'away_team',
        'odds',
    ];
}