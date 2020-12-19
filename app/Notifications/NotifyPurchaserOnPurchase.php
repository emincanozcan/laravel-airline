<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyPurchaserOnPurchase extends Notification implements ShouldQueue
{
    use Queueable;

    private $purchaseData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($purchaseData)
    {
        $this->purchaseData = $purchaseData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->markdown('email.purchase', [
            'name' => $this->purchaseData['name'],
            'passengerCount' => $this->purchaseData['passengerCount'],
            'flights' => $this->purchaseData['flights']
        ])->subject("Ticket Details | Nameless Airline");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
