<?php

namespace App\Jobs;

use App\Imports\FilesImport;
use App\Models\FileColumn;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ProcessFileImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $file, $fileRecord;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileName, $fileDBRecord)
    {
        $this->file = $fileName;
        $this->fileRecord = $fileDBRecord;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        HeadingRowFormatter::default('none');

        // Get headings of each sheet of the file
        $sheats = (new HeadingRowImport())->toArray($this->file, 'public');

        // Create columns records in file_columns table
        $columns = [];
        foreach ($sheats as $sheat) {
            foreach ($sheat as $headings) {
                foreach ($headings as $heading) {
                    $columns[] = new FileColumn(['name' => $heading]);
                }
            }
        }
        $this->fileRecord->columns()->saveMany($columns);
        Excel::import(new FilesImport($this->file), $this->file, 'public');
    }
}
