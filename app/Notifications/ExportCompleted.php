<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $filename,
        public string $path
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu exportación de gastos está lista')
            ->line('Hemos generado tu archivo CSV con los gastos solicitados.')
            ->action('Descargar Archivo', url('/downloads/'.urlencode($this->filename))) // Requeriría una ruta protegida
            ->line('Gracias por usar nuestra aplicación de gastos.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Tu exportación de gastos ha finalizado.',
            'filename' => $this->filename,
            'path' => $this->path,
            'action_url' => url('/downloads/'.urlencode($this->filename)),
        ];
    }
}
