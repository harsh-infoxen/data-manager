<?php

namespace App\Http\Controllers;

use App\Exports\ClientExport;
use App\Models\Clients;

use App\Imports\ClientImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Log;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{




    public function removeDuplicates(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);

        Log::debug($request);
        // $userId = Auth::user()->id;
        $file = $request->file('file')->store("uploads");


        // Check if the file has data
        $fileData = Excel::toArray([], $file);
        if (empty($fileData) || empty($fileData[0])) {
            return response()->json(['error' => 'No data found in the Excel file.'], 422);
        }

        $user = Auth::user();
        Log::debug($user);

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

            $cleanedData = array_merge([$headers], $newClientData);

            // Store cleaned data in cache
            Cache::put('cleaned_data', $cleanedData, now()->addMinutes(30));

            Log::debug($cleanedData);
            Log::info('Duplicates removed successfully.');

            return Excel::download(new ClientExport($cleanedData), 'removed_clients.xlsx');
        } catch (\Exception $e) {
            Log::error('Error during removal: ' . $e->getMessage());
            return response()->json(['error' => 'Error during import: ' . $e->getMessage()], 422);
        }
    }

    public function saveData(Request $request)
    {
        // Retrieve cleaned data from cache
        $cleanedData = Cache::get('cleaned_data');

        if (!$cleanedData) {
            return response()->json(['error' => 'Cleaned data not found. Please run removeDuplicates first.'], 422);
        }

        // Check if there is any data to save
        if (count($cleanedData) <= 1) {
            return response()->json(['error' => 'No data to save.'], 422);
        }

        try {
            $headers = array_shift($cleanedData);

            foreach ($cleanedData as $rowData) {
                // Log the data before saving
                Log::info('Data to be saved:', $rowData);

                $client = Clients::create([
                    "First Name" => $rowData[0],
                    "Last Name" => $rowData[1],
                    "email" => $rowData[2],
                    // Add more fields as needed
                ]);

                if (!$client) {
                    // Log an error if the data couldn't be saved
                    Log::error('Error saving data to the database:', $rowData);
                    return response()->json(['error' => 'Error saving data to the database.'], 422);
                }
            }

            // Remove cleaned data from cache after saving to the database
            Cache::forget('cleaned_data');

            return response()->json(['message' => 'File uploaded and processed successfully']);
        } catch (\Exception $e) {
            // Log any exceptions that occur during the process
            Log::error('Error during import: ' . $e->getMessage());

            return response()->json(['error' => 'Error during import: ' . $e->getMessage()], 422);
        }
    }


}