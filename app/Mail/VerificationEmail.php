<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Assinante;
use App\Models\App;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscriber;
    public $app;
    public $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Assinante $sub, App $app, $link)
    {
        $this->subscriber   = $sub;
        $this->app          = $app;
        $this->link         = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verification_mail')
                    ->subject(sprintf('%s - Verificação de email', $this->app->app_nome))
                    ->with([
                        'subscriber'    => $this->subscriber,
                        'currentApp'    => $this->app,
                        'link'          => $this->link,
                    ]);
    }
}
