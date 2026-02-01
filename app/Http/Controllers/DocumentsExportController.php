<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentsExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $user = $request->user();
        if (! $user || ! $user->organization_id) {
            abort(403);
        }

        $documents = Document::with(['form', 'formVersion', 'creator'])
            ->where('organization_id', $user->organization_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $dataKeys = [];
        foreach ($documents as $doc) {
            if (is_array($doc->data)) {
                $dataKeys = array_unique(array_merge($dataKeys, array_keys($doc->data)));
            }
        }
        sort($dataKeys);

        $headers = [
            __('common.title'),
            __('Form'),
            __('Version'),
            __('Status'),
            __('common.created_by'),
            __('common.created_at'),
        ];
        foreach ($dataKeys as $key) {
            $headers[] = $key;
        }

        return response()->streamDownload(function () use ($documents, $headers, $dataKeys) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, $headers);

            foreach ($documents as $doc) {
                $row = [
                    $doc->title,
                    $doc->form?->name ?? '',
                    $doc->formVersion?->version ?? '',
                    $doc->status ?? '',
                    $doc->creator?->name ?? '',
                    $doc->created_at?->format('Y-m-d H:i') ?? '',
                ];
                $data = is_array($doc->data) ? $doc->data : [];
                foreach ($dataKeys as $key) {
                    $value = $data[$key] ?? '';
                    $row[] = is_array($value) ? json_encode($value) : $value;
                }
                fputcsv($out, $row);
            }
            fclose($out);
        }, 'documents-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
