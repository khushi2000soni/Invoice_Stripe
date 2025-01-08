<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShareBackupFileMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    use Queueable, SerializesModels;

    public $backupFilePath;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @param string $backupFilePath
     * @param string $subject
     */
    public function __construct($backupFilePath, $subject)
    {
        $this->backupFilePath = $backupFilePath;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.backup.share_backup_mail')
            ->attach($this->backupFilePath, [
                'as' => 'backup_' . now()->format('d_M_Y_H_i_s') . '.sql',
                'mime' => 'application/sql',
            ])
            ->subject($this->subject);
    }
}
