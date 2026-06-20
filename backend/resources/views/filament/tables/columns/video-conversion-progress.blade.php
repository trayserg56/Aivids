@php
    /** @var \App\Models\Video $record */
    $status = $record->conversion_status;
    $progress = (int) $record->conversion_progress;
    $step = $record->conversion_step;
@endphp

<div class="min-w-[8rem] space-y-1">
    @if (\App\Support\VideoConversionStatus::isActive($status))
        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-700/80 dark:bg-gray-800">
            <div
                class="h-full rounded-full bg-primary-500 transition-all duration-500 ease-out"
                style="width: {{ max(4, min(100, $progress)) }}%"
            ></div>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            {{ $step ?? 'Обработка' }} · {{ $progress }}%
        </p>
    @elseif ($status === \App\Support\VideoConversionStatus::Failed)
        <p class="text-xs font-medium text-danger-500">{{ $step ?? 'Ошибка конвертации' }}</p>
    @else
        <p class="text-xs text-gray-500 dark:text-gray-400">—</p>
    @endif
</div>
