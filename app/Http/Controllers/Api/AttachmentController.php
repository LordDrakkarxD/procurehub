<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    private array $allowedTypes = [
        'bidding' => \App\Models\Bidding::class,
        'contract' => \App\Models\Contract::class,
        'supply_order' => \App\Models\SupplyOrder::class,
        'company' => \App\Models\Company::class,
    ];

    public function index(Request $request)
    {
        $query = Attachment::with('uploader:id,name')->latest();

        if ($request->filled('attachable_id')) {
            $class = $this->resolveType($request->attachable_type);
            $query->where('attachable_type', $class)
                ->where('attachable_id', $request->attachable_id);
        } elseif ($request->filled('token')) {
            $query->where('token', $request->token);
        } else {
            return response()->json(['message' => 'Informe o registro ou o token.'], 422);
        }

        return response()->json($query->get());
    }

    public function store(StoreAttachmentRequest $request)
    {
        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $payload = [
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
            'token' => $request->token,
        ];

        if ($request->filled('attachable_id')) {
            $class = $this->resolveType($request->attachable_type);
            $model = $class::findOrFail($request->attachable_id);
            $attachment = $model->attachments()->create($payload);
        } else {
            $payload['attachable_type'] = $this->resolveType($request->attachable_type);
            $payload['attachable_id'] = null;
            $attachment = Attachment::create($payload);
        }

        return response()->json($attachment->load('uploader:id,name'), 201);
    }

    public function destroy(Attachment $attachment)
    {
        Storage::disk($attachment->disk)->delete($attachment->path);
        $attachment->delete();

        return response()->json(null, 204);
    }

    private function resolveType(string $type): string
    {
        if (! isset($this->allowedTypes[$type])) {
            abort(422, 'Tipo de anexo inválido.');
        }

        return $this->allowedTypes[$type];
    }
}
