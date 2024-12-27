<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountManagementRequestApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $payday;
    public $pay_cut;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $payday, $pay_cut)
    {
        $this -> username = $username;
        $this -> payday = $payday;
        $this -> pay_cut = $pay_cut;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.account-management-request-approved');
    }
}
