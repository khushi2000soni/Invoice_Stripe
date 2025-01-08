<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShareInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $pdfContent;
    public $pdfFileName;
    public $customer_name;

    public function __construct($pdfContent, $pdfFileName,$customer_name)
    {
        $this->pdfContent = $pdfContent;
        $this->pdfFileName = $pdfFileName;
        $this->customer_name = $customer_name;
    }

    public function build()
    {
        //dd($this->customer_name ,$this->pdfFileName);
        return $this->view('emails.order.share-invoice-mail')->with([
            'customer_name' =>  $this->customer_name,
        ])
        ->attachData($this->pdfContent, $this->pdfFileName, [
            'mime' => 'application/pdf',
        ]);
    }
}
