<?php

namespace App\Enum;

enum ReviewKey: string
{
    case GAMEPLAY = 'gameplay';
    case GRAPHICS = 'graphics';
    case STORYLINE = 'storyline';
    case REPLAYABILITY = 'replayability';
    case SOUND_AND_MUSIC = 'sound_and_music';
    case PERFORMANCE = 'performance';
}
