<?php

namespace App\Enum;

enum HomepageSortingType: string
{
	case NEWEST = 'newest';
	case MOST_POPULAR = 'most_popular';
	case UPCOMING = 'upcoming';
}
