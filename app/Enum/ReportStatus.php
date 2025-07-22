<?php

namespace App\Enum;

enum ReportStatus: string
{
	case OPEN = 'open';
	case CLOSED = 'closed';
	case REJECTED = 'rejected';
}
