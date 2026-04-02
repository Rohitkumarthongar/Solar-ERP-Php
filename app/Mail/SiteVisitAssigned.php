<?php

namespace App\Mail;

use App\Models\SiteVisit;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SiteVisitAssigned extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $siteVisit;
    public $employee;
    public $assignedBy;

    /**
     * Create a new message instance.
     */
    public function __construct(SiteVisit $siteVisit, Employee $employee, $assignedBy = null)
    {
        $this->siteVisit = $siteVisit;
        $this->employee = $employee;
        $this->assignedBy = $assignedBy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Site Visit Assigned - ' . $this->siteVisit->visit_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.site-visit-assigned',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
