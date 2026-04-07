<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            'company_name' => 'Rajasthan Green Energy Solar Power Pvt. Ltd.',
            'company_tagline' => 'Domestic Rooftop Solar Solutions For Smart & Sustainable Homes',
            'company_logo' => 'images/print-format-presets/logo.png',
            'company_address' => 'Second Floor, BL Tower, Arihant Nagar, Kalwar Road, Hatoj, Jaipur (Rajasthan) 302012',
            'company_phone' => '+91-9785277913',
            'company_email' => 'headoffice@rajasthangreensolar.com',
            'company_website' => 'www.rajasthangreensolar.com',
            'gst_number' => '08AACCR9297Q1ZA',
            'bank_name' => 'AUSMALL FINANCE BANK',
            'bank_account_number' => '4444414141414141',
            'bank_ifsc' => 'AUBL0002444',
            'upi_id' => 'rajasthangreensolar@okaxis',
            'quotation_subsidy_amount' => '78000',
            'quotation_payment_terms' => "30% Advance.\n65% after structure delivered at site and before module & inverter dispatch.\n5% after solar and net meter installation.",
            'quotation_client_scope' => "Site-specific considerations will require assistance and cooperation from the client.\nProvide access to work site before delivery of equipment and materials.\nFacilitate access of work crew to the site for project execution.\nAll civil materials will be provided by client scope.",
            'quotation_vendor_scope' => "Prepare full system design including civil, structural, electrical and mechanical components with construction drawings.\nProcure equipment and materials and deliver to site.\nPerform complete system installation and commissioning.\nAssist for coordination with nodal agencies where applicable.",
            'quotation_project_completion' => '30-45 days from the date of receipt of solar NOC or commercial clearance order and advance payment.',
            'quotation_net_metering' => 'Customer shall bear utility or government charges for increasing load and related approvals required for net metering.',
            'quotation_warranty' => 'Solar module performance warranty: 25-30 years. Solar grid tie inverter: 7-10 years (as per manufacturer norms).',
            'quotation_offer_validity' => '15 days from the date of offer. After this period, confirmation from the vendor is required before proceeding.',
            'quotation_special_note_1' => 'Actual capacity of the system may vary during detailed engineering, in which case the project cost will be computed on a per watt basis based on this proposal.',
            'quotation_special_note_2' => 'As per applicable guidelines, solar PV system capacity may depend on site transformer and connection conditions. Any issue arising from site availability remains in client scope.',
            'quotation_special_note_3' => 'Cleaning of the solar plant remains in the scope of the client.',
            'quotation_special_note_4' => 'The quoted amount includes survey, layout planning, load analysis, design, development, supply, erection and commissioning of the solar PV power system.',
            'quotation_special_note_5' => 'Depreciation benefits, if any, shall be applicable as per actual policy and tax treatment.',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'company']);
        }
    }
}

