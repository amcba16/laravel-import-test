<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessCsvUpload;

class UploadController extends Controller
{
    public function index()
    {
        $uploads = Upload::latest()->get();

        return view('uploads.index', compact('uploads'));
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);
        
        // Store the CSV in storage/app/public/uploads
        $file = $request->file('csv_file');
        $path = $file->store('uploads', 'public');
        
        // Create upload record
        $upload = Upload::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'total_products' => 0,
            'processed_products' => 0,
        ]);
        
        // Dispatch async job to process CSV
        ProcessCsvUpload::dispatch($upload);

        return redirect()->back()->with('success', 'CSV uploaded successfully! Processing started.');
    }
}
