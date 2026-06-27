<?php

namespace App\Notifications;

use App\Models\AuditorRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuditorAssignedToCompanyNotification extends Notification
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
            ->subject('Auditor asignado a tu empresa - CheckData AI')
            ->greeting('¡Auditor asignado!')
            ->line("Se ha asignado un auditor especializado para tu empresa **{$req->company->name}**.")
            ->line("Auditor asignado: **{$req->assignedAuditor->name}** ({$req->assignedAuditor->email})")
            ->line('El auditor podrá revisar tu diagnóstico y brindarte asesoría personalizada.')
            ->action('Ir al dashboard', url('/dashboard'))
            ->salutation('Equipo CheckData AI');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'auditor_request_id' => $this->auditorRequest->id,
            'company_name' => $this->auditorRequest->company->name,
            'auditor_name' => $this->auditorRequest->assignedAuditor?->name,
            'type' => 'auditor_assigned',
        ];
    }
}
