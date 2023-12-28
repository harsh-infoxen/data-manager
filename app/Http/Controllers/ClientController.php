<?php

namespace App\Http\Controllers;

use App\Exports\ClientExport;
use App\Models\Clients;

use App\Imports\ClientImport;
use Illuminate\Http\Request;
use Log;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{

    public function removeDuplicates(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        $file = $request->file('file')->store('uploads');

        try {
            Excel::import(new ClientImport, $file);
            $clientData = Excel::toArray([], $file)[0];
            $headers = array_shift($clientData);
            $newClientData = [];
            foreach ($clientData as $row) {
                $email = $row[2];
                $existingClient = Clients::where('email', $email)->first();
                if (!$existingClient) {
                    array_push($newClientData, $row);
                }
            }
            $newClientData = array_merge([$headers], $newClientData);
            Log::debug($newClientData);
            return Excel::download(new ClientExport($newClientData), 'removed_clients.xlsx');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error during import: ' . $e->getMessage()], 422);
        }

        // return response()->json(['message' => 'Clients imported successfully'], 200);
    }
}