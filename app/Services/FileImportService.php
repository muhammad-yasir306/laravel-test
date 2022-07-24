<?php

namespace App\Services;

use App\Imports\FilesImport;
use App\Jobs\ProcessFileImport;
use App\Models\File;
use App\Models\FileColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class FileImportService
{
    /**
     * @param $file
     * @return void
     */
    public function importDataFromFileInToDatabase($file)
    {
        // Save file in local storage

        $fileName = $file->hashName();
        Storage::putFile('public', $file);

        // Create file record in files table
        $fileRecord = File::create([
            'name' => $fileName
        ]);

        // Dispatch the job import file data into database
        ProcessFileImport::dispatch($fileName, $fileRecord)->onQueue('high');
    }
}
