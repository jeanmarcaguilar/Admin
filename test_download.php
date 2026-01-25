<!DOCTYPE html>
<html>
<head>
    <title>Test Download</title>
</head>
<body>
    <h1>Download Test</h1>
    
    <?php
    // Get documents from database
    $documents = \App\Models\Document::orderByDesc('created_at')->get()->map(function ($d) {
        return [
            'id' => $d->code ?? '',
            'db_id' => $d->id,
            'code' => $d->code ?? '',
            'name' => $d->name ?? '',
            'type' => $d->type ?? '',
            'category' => $d->category ?? '',
            'size' => $d->size_label ?? '0 MB',
            'uploaded' => ($d->uploaded_on ?: ($d->created_at?->toDateString() ?? now()->toDateString())),
            'status' => $d->status ?? 'Indexed',
        ];
    })->toArray();
    ?>
    
    <h2>Available Documents:</h2>
    <ul>
        <?php foreach ($documents as $doc): ?>
            <li>
                <strong><?php echo htmlspecialchars($doc['name']); ?></strong> 
                (ID: <?php echo htmlspecialchars($doc['id']); ?>)
                <a href="<?php echo route('document.download', $doc['id']); ?>" download>Download</a>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <h2>Available Files in Storage:</h2>
    <ul>
        <?php
        $files = Illuminate\Support\Facades\Storage::disk('public')->allFiles('documents');
        foreach ($files as $file): ?>
            <li><?php echo htmlspecialchars($file); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
