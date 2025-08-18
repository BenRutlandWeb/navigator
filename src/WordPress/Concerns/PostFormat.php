<?php

namespace Navigator\WordPress\Concerns;

enum PostFormat: string
{
    case STANDARD = 'standard';
    case ASIDE = 'aside';
    case CHAT = 'chat';
    case GALLERY = 'gallery';
    case LINK = 'link';
    case IMAGE = 'image';
    case QUOTE = 'quote';
    case STATUS = 'status';
    case VIDEO = 'video';
    case AUDIO = 'audio';
}
