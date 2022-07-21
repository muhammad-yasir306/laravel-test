<?php

namespace App\Http\Controllers;

use App\Services\FileImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        if ($request->hasFile('sheet')) {
            $file = $request->file('sheet');

            Validator::make(
                [
                    'file'      => $file,
                    'extension' => strtolower($file->getClientOriginalExtension()),
                ],
                [
                    'file'          => 'required',
                    'extension'      => 'required|in:csv,xlsx,xls',
                ]
            )->validate();
            (new FileImportService())->importDataFromFileInToDatabase($file);
            return redirect('/')->with('message', 'Uploaded successfully!');
        }
        return redirect()->back()->withInput()->withErrors(['Kindly select a file to upload']);
    }
}
