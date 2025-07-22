<?php

namespace App\Enum;

enum ReportReason: string
{
	case HARASSMENT_OR_BULLYING = 'Harassment or bullying';
	case HATE_SPEECH = 'Hate speech';
	case SPAM_OR_SELF_PROMOTION = 'Spam or self-promotion';
	case INAPPROPRIATE_CONTENT = 'Inappropriate content';
	case CHEATING_OR_EXPLOITS = 'Cheating or exploits';
	case IMPERSONATION = 'Impersonation';
	case OFF_TOPIC_CONTENT = 'Off-topic content';
	case ILLEGAL_CONTENT = 'Illegal content';
	case COPYRIGHT_INFRINGEMENT = 'Copyright infringement';
	case FALSE_INFORMATION = 'False information';
	case TROLLING = 'Trolling';
	case PERSONAL_INFORMATION = 'Personal information';
	case INAPPROPRIATE_USERNAME = 'Inappropriate username';
	case REVIEW_MANIPULATION = 'Review manipulation';
	case SCAM_OR_FRAUD = 'Scam or fraud';
}
