<?php

namespace App\Livewire\Notifications;

use Livewire\Component;

class ToastNotification extends Component
{
    public $show = false;
    public $message = '';
    public $type = 'success'; // success, error, warning, info
    public $duration = 5000; // 5 segundos por defecto

    protected $listeners = [
        'notify' => 'showNotification',
        'notify-success' => 'notifySuccess',
        'notify-error' => 'notifyError',
        'notify-warning' => 'notifyWarning',
        'notify-info' => 'notifyInfo',
    ];

    /**
     * Mostrar notificación genérica
     */
    public function showNotification($message, $type = 'success', $duration = 5000)
    {
        $this->message = $message;
        $this->type = $type;
        $this->duration = $duration;
        $this->show = true;

        // Auto-hide después del duration especificado
        $this->dispatch('auto-hide-notification', duration: $duration);
    }

    /**
     * Notificación de éxito
     */
    public function notifySuccess($message, $duration = 5000)
    {
        $this->showNotification($message, 'success', $duration);
    }

    /**
     * Notificación de error
     */
    public function notifyError($message, $duration = 7000)
    {
        $this->showNotification($message, 'error', $duration);
    }

    /**
     * Notificación de advertencia
     */
    public function notifyWarning($message, $duration = 6000)
    {
        $this->showNotification($message, 'warning', $duration);
    }

    /**
     * Notificación informativa
     */
    public function notifyInfo($message, $duration = 5000)
    {
        $this->showNotification($message, 'info', $duration);
    }

    /**
     * Ocultar notificación
     */
    public function hide()
    {
        $this->show = false;
        $this->message = '';
    }

    /**
     * Renderizar componente
     */
    public function render()
    {
        return view('livewire.notifications.toast-notification');
    }
}
