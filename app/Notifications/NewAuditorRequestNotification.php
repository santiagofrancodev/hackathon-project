<?php

namespace App\Notifications;

use App\Models\AuditorRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAuditorRequestNotification extends Notification
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
            ->subject('Nueva solicitud de auditoría - CheckData AI')
            ->greeting('Nueva solicitud de auditoría')
            ->line("La empresa **{$req->company->name}** ha solicitado la asignación de un auditor especializado.")
            ->line("Evaluación: **{$req->assessment->score}%** de cumplimiento.")
            ->line("Solicitante: {$req->requester->name} ({$req->requester->email})")
            ->when($req->notes, fn ($msg) => $msg->line("Notas: {$req->notes}"))
            ->action('Ver solicitudes', url('/dashboard'))
            ->salutation('Equipo CheckData AI');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auditor_request_id' => $this->auditorRequest->id,
            'company_name' => $this->auditorRequest->company->name,
            'score' => $this->auditorRequest->assessment->score,
            'requester_name' => $this->auditorRequest->requester->name,
            'status' => 'pending',
        ];
    }
}
