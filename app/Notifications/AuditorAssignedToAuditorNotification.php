<?php

namespace App\Notifications;

use App\Models\AuditorRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuditorAssignedToAuditorNotification extends Notification
{
    use Queueable;

    public AuditorRequest $auditorRequest;

    public function __construct(AuditorRequest $auditorRequest)
    {
        $this->auditorRequest = $auditorRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $req = $this->auditorRequest;

        return (new MailMessage)
            ->subject('Has sido asignado como auditor - CheckData AI')
            ->greeting('¡Nueva asignación!')
            ->line("Has sido asignado como auditor para la empresa **{$req->company->name}**.")
            ->line("Resultado del diagnóstico: **{$req->assessment->score}%**")
            ->line('Puedes revisar el detalle del diagnóstico y descargar el PDF desde tu dashboard.')
            ->action('Ir al dashboard', url('/dashboard'))
            ->salutation('Equipo CheckData AI');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auditor_request_id' => $this->auditorRequest->id,
            'company_name' => $this->auditorRequest->company->name,
            'score' => $this->auditorRequest->assessment->score,
            'type' => 'assigned_as_auditor',
        ];
    }
}
