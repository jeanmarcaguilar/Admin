<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/test-upload', function () {
    return view('test-upload');
});

Route::post('/test-upload', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'document' => 'required|file|max:51200', // 50MB
    ]);

    $file = $request->file('document');
    $path = $file->store('documents', 'public');
    
    $document = \App\Models\Document::create([
        'code' => 'TEST-' . date('YmdHis'),
        'name' => $file->getClientOriginalName(),
        'type' => $file->getClientOriginalExtension(),
        'category' => 'test',
        'size' => $file->getSize(),
        'size_label' => formatBytes($file->getSize()),
        'file_path' => $path,
        'file_type' => $file->getMimeType(),
        'description' => 'Test upload',
        'status' => 'Indexed',
        'uploaded_on' => now()->toDateString(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'File uploaded successfully',
        'document' => $document
    ]);
});
