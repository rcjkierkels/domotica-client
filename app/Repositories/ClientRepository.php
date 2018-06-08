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
     * Source: https://www.codeproject.com/Questions/991416/How-Do-I-Get-Mac-Address-Of-Client-Using-Php
     * @return string
     */
    protected function getMacLinux() {
        exec('netstat -ie', $result);
        if(is_array($result)) {
            $iface = array();
            foreach($result as $key => $line) {
                if($key > 0) {
                    $tmp = str_replace(" ", "", substr($line, 0, 10));
                    if($tmp <> "") {
                        $macpos = strpos($line, "HWaddr");
                        if($macpos !== false) {
                            $iface[] = array('iface' => $tmp, 'mac' => strtolower(substr($line, $macpos+7, 17)));
                        }
                    }
                }
            }
            return $iface[0]['mac'];
        } else {
            return "notfound";
        }
    }

}