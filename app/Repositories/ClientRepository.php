<?php

namespace App\Repositories;

use App\Models\Client;
use Carbon\Carbon;

class ClientRepository
{

    public function insertOrUpdateClient(array $extraData = [])
    {
        $macAddress = $this->getMacLinux();

        $data = [
            'ip_address' => $this->getIpAddress(),
            'hostname' => gethostname(),
            'name' => config('client.name'),
            'location' => config('client.location'),
            'updated_at' => Carbon::now()
        ];

        $data = array_merge($data, $extraData);

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

    protected function getIpAddress() {

        $networkInterface = "ip route show default | awk '/default/ {print $5}'";
        $getIpAddress = 'ifconfig '."$($networkInterface)".'| sed -En \'s/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p\'';
        exec($getIpAddress, $result);

        if (!empty($result)) {
            return current($result);
        }

        return gethostbyname(gethostname());
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