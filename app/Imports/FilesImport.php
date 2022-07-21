<?php

namespace App\Imports;

use App\Models\File;
use App\Models\FileData;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;

class FilesImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $file = File::latest()->first();
        foreach ($file->columns as $column) {
            $data = $row[$column->name] ?? null;
            if (!empty($data)) {
                FileData::create([
                    'column_id' => $column->id,
                    'data' => $data
                ]);
            }
        }
    }
}
