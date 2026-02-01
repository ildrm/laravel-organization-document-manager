<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Form;
use Illuminate\Support\Collection;

class ReportsFieldChartsService
{
    /**
     * Get chartable field keys from schema (exclude file/image/rich_text which don't aggregate well).
     */
    public function getChartableFieldKeys(array $schema): array
    {
        $chartableTypes = ['text', 'textarea', 'number', 'email', 'date', 'solar_date', 'time', 'select', 'radio', 'checkbox', 'switch', 'phone'];
        $fields = [];

        foreach ($schema as $block) {
            $type = $block['type'] ?? null;
            $data = $block['data'] ?? [];
            if (! $type || ! isset($data['key'])) {
                continue;
            }
            if (in_array($type, $chartableTypes, true)) {
                $label = $data['label']['en'] ?? $data['label']['fa'] ?? $data['key'];
                $fields[$data['key']] = is_array($label) ? ($label['en'] ?? $data['key']) : $label;
            }
        }

        return $fields;
    }

    /**
     * For a given form and field key, return value => count for documents in the org.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    public function getFieldValueCounts(int $organizationId, int $formId, string $fieldKey, ?int $limit = 15): array
    {
        $documents = Document::query()
            ->where('organization_id', $organizationId)
            ->where('form_id', $formId)
            ->get();

        $counts = [];
        foreach ($documents as $doc) {
            $value = $doc->data[$fieldKey] ?? null;
            if ($value === null || $value === '') {
                $label = __('(empty)');
            } else {
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                $label = (string) $value;
            }
            $counts[$label] = ($counts[$label] ?? 0) + 1;
        }

        arsort($counts);
        $counts = array_slice($counts, 0, $limit, true);

        return [
            'labels' => array_keys($counts),
            'data' => array_values($counts),
        ];
    }

    /**
     * Get forms that have documents in the organization, with their schema and field chart data.
     *
     * @return array<int, array{form: Form, formName: string, fieldCharts: array<string, array{label: string, labels: array, data: array}>}>
     */
    public function getFormsWithFieldCharts(int $organizationId): array
    {
        $forms = Form::query()
            ->whereHas('documents', fn ($q) => $q->where('organization_id', $organizationId))
            ->with(['currentVersion', 'latestPublishedVersion'])
            ->get();

        $result = [];
        foreach ($forms as $form) {
            $version = $form->currentVersion ?? $form->latestPublishedVersion;
            if (! $version || empty($version->schema)) {
                continue;
            }

            $fieldKeys = $this->getChartableFieldKeys($version->schema);
            if (empty($fieldKeys)) {
                continue;
            }

            $fieldCharts = [];
            foreach ($fieldKeys as $key => $label) {
                $chart = $this->getFieldValueCounts($organizationId, $form->id, $key);
                if (! empty($chart['labels'])) {
                    $fieldCharts[$key] = [
                        'label' => $label,
                        'labels' => $chart['labels'],
                        'data' => $chart['data'],
                    ];
                }
            }

            if (! empty($fieldCharts)) {
                $result[] = [
                    'form' => $form,
                    'formName' => $form->name,
                    'fieldCharts' => $fieldCharts,
                ];
            }
        }

        return $result;
    }
}
