<?php

namespace App\Collections;

use App\Objects\EventAttachment;

class EventAttachmentCollection extends TypeCollection
{
    function getValidType(): string
    {
        return EventAttachment::class;
    }
}
