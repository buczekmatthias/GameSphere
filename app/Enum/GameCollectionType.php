<?php

namespace App\Enum;

enum GameCollectionType: string
{
	case WISHLIST = 'wishlist';
	case OWNED = 'owned';
	case PLAYING = 'playing';
	case COMPLETED = 'completed';
	case FAVORITE = 'favorite';
}
