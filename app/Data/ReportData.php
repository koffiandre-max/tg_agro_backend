<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Max;

class ReportData extends Data
{
    #[Required]
    #[StringType]
    public string $title;

    #[Required]
    #[Exists('farms', 'id')]
    public int $farm_id;

    #[Required]
    #[Exists('clients', 'id')]
    public int $client_id;

    #[Nullable]
    #[Exists('users', 'id')]
    public ?int $technician_id;

    #[Required]
    #[StringType]
    public string $type;

    #[Required]
    #[StringType]
    public string $file_path;

    #[Nullable]
    #[StringType]
    #[Max(255)]
    public ?string $file_original_name;

    #[Nullable]
    #[IntegerType]
    public ?int $file_size;

    #[Nullable]
    #[StringType]
    public ?string $notes;

    #[StringType]
    public string $status = 'pending';

    #[Nullable]
    #[StringType]
    #[Max(1000)]
    public ?string $rejection_reason = null;

    public static function fromModel(\App\Models\Report $report): self
    {
        return new self([
            'title' => $report->title,
            'farm_id' => $report->farm_id,
            'client_id' => $report->client_id,
            'technician_id' => $report->technician_id,
            'type' => $report->type,
            'file_path' => $report->file_path,
            'file_original_name' => $report->file_original_name,
            'file_size' => $report->file_size,
            'notes' => $report->notes,
            'status' => $report->status,
            'rejection_reason' => $report->rejection_reason,
        ]);
    }
}
