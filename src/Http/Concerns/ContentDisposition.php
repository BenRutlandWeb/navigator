<?php

namespace Navigator\Http\Concerns;

enum ContentDisposition: string
{
    case INLINE = 'inline';
    case ATTACHMENT = 'attachment';
}
