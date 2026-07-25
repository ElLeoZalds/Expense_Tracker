<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExportCompleted;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportExpensesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?Carbon $startDate = null,
        public ?Carbon $endDate = null
    ) {}

    public function handle(): void
    {
        $query = Expense::where('user_id', $this->user->id);

        if ($this->startDate) {
            $query->where('date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->where('date', '<=', $this->endDate);
        }

        $expenses = $query->with('category')->get();

        $filename = 'expenses_export_'.$this->user->id.'_'.Str::uuid().'.csv';
        $path = Storage::disk('private')->put($filename, $this->generateCsvContent($expenses));

        // Notificar al usuario (asumiendo que existe la notificación DownloadReady)
        // Si no existe, se puede crear una genérica o usar DatabaseNotification directamente.
        $this->user->notify(new ExportCompleted($filename, $path));
    }

    private function generateCsvContent($expenses): string
    {
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, ['ID', 'Date', 'Description', 'Category', 'Amount', 'Notes']);

        foreach ($expenses as $expense) {
            fputcsv($handle, [
                $expense->id,
                $expense->date->format('Y-m-d'),
                $expense->description,
                $expense->category?->name ?? 'N/A',
                $expense->amount,
                $expense->notes ?? '',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }
}
