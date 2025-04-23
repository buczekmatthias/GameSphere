<?php

namespace App\Enum;

enum UserRole: string
{
	case USER = 'user';
	case GAME_CREATOR = 'game_creator';
	case MODERATOR = 'moderator';
	case ADMIN = 'admin';
	case DEVELOPER = 'developer';
}
