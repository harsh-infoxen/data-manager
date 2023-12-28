<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ClientExport implements FromCollection
{
   protected $client;
   public function __construct($client)
   {
    $this->client = $client;
   }
    public function collection()
    {
        return collect($this->client);
    }
}
