<?php

namespace App\Repositories;

use App\Enums\ActionType;
use App\Models\Action;
use App\Models\Event;
use Illuminate\Support\Collection;

class ActionRepository
{

    public function getSpecificActionsForEvent(Event $event, array $actionType) : ?Collection
    {
        $actions = Action::where('task_id', $event->task_id)
            ->whereIn('type', $actionType)
            ->get();

        $evaluatedActions = $actions->filter(function ($action) use ($event) {
            /** @var Action $action */
            return $action->evaluate($event);
        });

        if (empty($evaluatedActions)) {
            return null;
        }

        return $evaluatedActions;
    }

}
