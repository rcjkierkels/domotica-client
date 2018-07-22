<?php

namespace App\Repositories;

use App\Models\Client;
use Carbon\Carbon;

class ClientRepository
{

    public function insertOrUpdateClient()
    {
        $macAddress = $this->getMacLinux();

        $data = [
            'ip_address' => gethostbyname(gethostname()),
            'hostname' => gethostname(),
            'name' => config('client.name'),
            'location' => config('client.location'),
            'updated_at' => Carbon::now()
        ];

        $client = Client::where('physical_address', $macAddress)->first();

        if (empty($client)) {
            $client = new Client();
            $client->physical_address = $macAddress;
        }

        $client->fill($data);
        $client->save();

        return $client;
    }

    public function getClient(): Client
    {
        $client = Client::where('physical_address', $this->getMacLinux())->first();

        if (empty($client)) {
            return $this->insertOrUpdateClient();
        }

        return $client;
    }

    /**
     * Source: https://stackoverflow.com/questions/23828413/get-mac-address-using-shell-script
     * @return string
     */
    protected function getMacLinux() {
        exec("cat /sys/class/net/$(ip route show default | awk '/default/ {print $5}')/address", $result);
        if (!empty($result)) {
            return current($result);
        }

        return 'unknown';
    }

}