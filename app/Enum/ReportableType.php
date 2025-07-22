<?php

namespace App\Enum;

enum ReportableType: string
{
	case DISCUSSION = 'discussion';
	case COMMENT = 'comment';
	case GAME = 'game';
	case REVIEW = 'review';
}
