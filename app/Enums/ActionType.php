<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ActionType extends Enum
{
    const PUSH = 'push';
    const EXEC = 'exec';
    const MAIL = 'mail';
    const TASK = 'task';
}
