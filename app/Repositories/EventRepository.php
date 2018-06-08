<?php

namespace App\Repositories;

use App\Models\Client;
use App\Models\Event;

class EventRepository
{
    protected $clientRepository;

    /** @var \App\Models\Client client */
    protected $client;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->client = $this->clientRepository->getClient();
    }

    public function triggerEvent(string $name, $data)
    {
        $event = new Event();
        $event->client_id = $this->client->id;
        $event->event = $name;
        $event->data = json_encode($data);
        $event->save();
    }



}