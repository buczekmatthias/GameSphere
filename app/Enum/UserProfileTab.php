<?php

namespace App\Enum;

enum UserProfileTab: string
{
    case CREATED_GAMES = 'created_games';
    case GAMES = 'games';
    case GENRES = 'genres';
    case REVIEWS = 'reviews';
    case DISCUSSIONS = 'discussions';
    case COMMENTS = 'comments';
}
