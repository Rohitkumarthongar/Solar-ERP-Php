<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SmsTemplate;
use App\Models\SmsConfiguration;

class SmsSeeder extends Seeder
{
    public function run(): void
    {
        SmsConfiguration::firstOrCreate(
            ['provider' => 'twilio'],
            ['is_active' => false, 'sender_id' => 'SOLRTP', 'from_number' => '']
        );

        $templates = [
            ['name' => 'Lead Received', 'type' => 'lead_received', 'message' => 'Hi {name}, thank you for your solar enquiry! Your reference number is {lead_number}. Our expert will call you within 24 hours. - {company}'],
            ['name' => 'Thank You (Website)', 'type' => 'thank_you', 'message' => 'Dear {name}, we received your solar quote request ({lead_number}). Our team will contact you shortly. - {company}'],
            ['name' => 'Quotation Sent', 'type' => 'quotation_sent', 'message' => 'Dear {name}, your solar quotation {quotation_number} for {amount} is ready! Valid till {valid_until}. Contact us for queries. - {company}'],
            ['name' => 'Order Confirmed', 'type' => 'order_confirmed', 'message' => 'Hi {name}, your solar system order is confirmed! Our team will schedule installation soon. - {company}'],
            ['name' => 'Installation Scheduled', 'type' => 'installation_scheduled', 'message' => 'Dear {name}, your solar installation ({installation_number}) is scheduled on {scheduled_date}. Our team will visit your site. - {company}'],
            ['name' => 'Installation Completed', 'type' => 'installation_completed', 'message' => 'Congratulations {name}! Your solar system is now installed and active. Welcome to the solar family! - {company}'],
            ['name' => 'Service Created', 'type' => 'service_created', 'message' => 'Hi {name}, a service request has been created for your solar system. Our technician will contact you to schedule a visit. - {company}'],
            ['name' => 'Follow Up', 'type' => 'follow_up', 'message' => 'Hi {name}, we wanted to follow up on your solar enquiry. Would you like to proceed? - {company}'],
        ];

        foreach ($templates as $t) {
            SmsTemplate::updateOrCreate(['type' => $t['type']], array_merge($t, ['is_active' => true]));
        }
    }
}
