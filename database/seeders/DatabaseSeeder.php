<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Installation;
use App\Models\ServiceRequest;
use App\Models\Employee;
use App\Models\SalaryRecord;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Models\Notification;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $adminRole = Role::create(['name' => 'admin', 'description' => 'Full access', 'permissions' => json_encode(['dashboard','customers','leads','quotations','sales_orders','purchase_orders','products','packages','inventory','installations','services','employees','reports','settings','notifications','roles'])]);
        $salesRole = Role::create(['name' => 'sales', 'description' => 'Sales access', 'permissions' => json_encode(['dashboard','customers','leads','quotations','sales_orders'])]);
        $techRole = Role::create(['name' => 'technician', 'description' => 'Field tech access', 'permissions' => json_encode(['dashboard','installations','services'])]);

        // Admin Users
        AdminUser::create(['name' => 'Super Admin', 'email' => 'admin@solarerp.com', 'password' => Hash::make('admin123'), 'role' => 'admin', 'role_id' => $adminRole->id, 'is_active' => true]);
        AdminUser::create(['name' => 'Sales Manager', 'email' => 'sales@solarerp.com', 'password' => Hash::make('sales123'), 'role' => 'sales', 'role_id' => $salesRole->id, 'is_active' => true]);
        AdminUser::create(['name' => 'Tech Lead', 'email' => 'tech@solarerp.com', 'password' => Hash::make('tech123'), 'role' => 'technician', 'role_id' => $techRole->id, 'is_active' => true]);

        // Settings
        $settingsData = [
            ['key' => 'company_name', 'value' => 'Palawat Solar', 'group' => 'general'],
            ['key' => 'company_tagline', 'value' => 'Powering a Greener Tomorrow', 'group' => 'general'],
            ['key' => 'company_email', 'value' => 'info@solartech.com', 'group' => 'general'],
            ['key' => 'company_phone', 'value' => '+91 98765 43210', 'group' => 'general'],
            ['key' => 'company_address', 'value' => '123 Solar Park, Green City, Gujarat - 380001', 'group' => 'general'],
            ['key' => 'company_gst', 'value' => '24AABCS1429B1Z1', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'INR', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => '₹', 'group' => 'general'],
            ['key' => 'website_hero_title', 'value' => 'Switch to Solar, Save More', 'group' => 'website'],
            ['key' => 'website_hero_subtitle', 'value' => 'Premium solar solutions for homes and businesses', 'group' => 'website'],
            ['key' => 'mail_driver', 'value' => 'smtp', 'group' => 'email'],
            ['key' => 'mail_host', 'value' => 'smtp.gmail.com', 'group' => 'email'],
            ['key' => 'mail_port', 'value' => '587', 'group' => 'email'],
            ['key' => 'mail_from_address', 'value' => 'info@solartech.com', 'group' => 'email'],
            ['key' => 'mail_from_name', 'value' => 'Palawat Solar', 'group' => 'email'],
        ];
        foreach ($settingsData as $s) { Setting::create($s); }

        // Email Templates
        EmailTemplate::create(['name' => 'Quotation Email', 'type' => 'quotation', 'subject' => 'Your Solar System Quotation - {quotation_number}', 'body' => '<p>Dear {customer_name},</p><p>Please find attached your quotation <strong>{quotation_number}</strong> for a solar system installation.</p><p><strong>Total Amount: ₹{total_amount}</strong></p><p>This quotation is valid until {valid_until}.</p><p>Please feel free to contact us for any queries.</p><p>Best regards,<br>Palawat Solar Team</p>', 'is_active' => true]);
        EmailTemplate::create(['name' => 'Follow Up Email', 'type' => 'follow_up', 'subject' => 'Following up on your Solar Inquiry', 'body' => '<p>Dear {customer_name},</p><p>We wanted to follow up on your recent inquiry about solar panel installation.</p><p>Our team is ready to provide you with a customized solution.</p><p>Best regards,<br>Palawat Solar</p>', 'is_active' => true]);
        EmailTemplate::create(['name' => 'Welcome Email', 'type' => 'welcome', 'subject' => 'Welcome to SolarTech Family!', 'body' => '<p>Dear {customer_name},</p><p>Welcome to Palawat Solar! We are thrilled to have you as our customer.</p><p>Your order <strong>{order_number}</strong> has been confirmed.</p><p>Best regards,<br>Palawat Solar Team</p>', 'is_active' => true]);

        // Packages
        $pkg1 = Package::create(['name' => '3kW Home Starter Pack', 'description' => 'Perfect for small homes with basic appliances', 'system_size_kw' => 3, 'price' => 185000, 'suitable_for' => 'residential', 'includes' => '6x 500W Solar Panels, 3kW Inverter, Mounting Structure, MC4 Connectors, 25 Year Panel Warranty, 5 Year Installation Warranty', 'warranty_years' => 25, 'is_active' => true, 'is_featured' => true]);
        $pkg2 = Package::create(['name' => '5kW Premium Home Pack', 'description' => 'Ideal for medium homes, covers all appliances', 'system_size_kw' => 5, 'price' => 290000, 'suitable_for' => 'residential', 'includes' => '10x 500W Solar Panels, 5kW Hybrid Inverter, Battery Backup, Mounting Structure, Smart Monitoring, 25 Year Panel Warranty', 'warranty_years' => 25, 'is_active' => true, 'is_featured' => true]);
        $pkg3 = Package::create(['name' => '10kW Business Pack', 'description' => 'For small businesses and commercial establishments', 'system_size_kw' => 10, 'price' => 550000, 'suitable_for' => 'commercial', 'includes' => '20x 500W Solar Panels, 10kW Commercial Inverter, Mounting Structure, Net Metering, AMC for 5 Years', 'warranty_years' => 25, 'is_active' => true, 'is_featured' => true]);
        $pkg4 = Package::create(['name' => '25kW Industrial Pack', 'description' => 'Large scale industrial solar solution', 'system_size_kw' => 25, 'price' => 1250000, 'suitable_for' => 'industrial', 'includes' => '50x 500W Solar Panels, 25kW Industrial Inverter, Heavy Duty Mounting, SCADA Monitoring, 10 Year AMC', 'warranty_years' => 25, 'is_active' => true, 'is_featured' => false]);

        // Products
        $products = [
            ['name' => 'Luminous 500W Mono PERC Panel', 'sku' => 'SP-LUM-500', 'category' => 'solar_panel', 'brand' => 'Luminous', 'purchase_price' => 12000, 'selling_price' => 15000, 'unit' => 'piece', 'warranty_months' => 300],
            ['name' => 'Waaree 550W Bi-Facial Panel', 'sku' => 'SP-WAR-550', 'category' => 'solar_panel', 'brand' => 'Waaree', 'purchase_price' => 14000, 'selling_price' => 17500, 'unit' => 'piece', 'warranty_months' => 300],
            ['name' => 'Growatt 3kW Hybrid Inverter', 'sku' => 'INV-GRW-3KW', 'category' => 'inverter', 'brand' => 'Growatt', 'purchase_price' => 28000, 'selling_price' => 35000, 'unit' => 'piece', 'warranty_months' => 60],
            ['name' => 'Solis 5kW On-Grid Inverter', 'sku' => 'INV-SOL-5KW', 'category' => 'inverter', 'brand' => 'Solis', 'purchase_price' => 42000, 'selling_price' => 52000, 'unit' => 'piece', 'warranty_months' => 60],
            ['name' => 'Luminous 150Ah Solar Battery', 'sku' => 'BAT-LUM-150', 'category' => 'battery', 'brand' => 'Luminous', 'purchase_price' => 15000, 'selling_price' => 19000, 'unit' => 'piece', 'warranty_months' => 36],
            ['name' => 'GI Mounting Structure 3kW', 'sku' => 'MNT-GI-3KW', 'category' => 'mounting', 'brand' => 'Generic', 'purchase_price' => 8000, 'selling_price' => 11000, 'unit' => 'set', 'warranty_months' => 120],
            ['name' => 'MC4 Solar Cable 4mm 100m', 'sku' => 'CAB-MC4-4MM', 'category' => 'cable', 'brand' => 'Polycab', 'purchase_price' => 4500, 'selling_price' => 6000, 'unit' => 'roll', 'warranty_months' => null],
            ['name' => 'DCB 10kW Commercial Inverter', 'sku' => 'INV-DCB-10KW', 'category' => 'inverter', 'brand' => 'DCB', 'purchase_price' => 85000, 'selling_price' => 105000, 'unit' => 'piece', 'warranty_months' => 60],
        ];
        $productIds = [];
        foreach ($products as $p) {
            $prod = Product::create(array_merge($p, ['is_active' => true]));
            Inventory::create(['product_id' => $prod->id, 'quantity' => rand(10, 50), 'min_quantity' => 5]);
            $productIds[] = $prod->id;
        }

        // Customers
        $customers = [
            ['customer_code' => 'CUST-RES-1001', 'name' => 'Rajesh Kumar', 'email' => 'rajesh@gmail.com', 'phone' => '9876543201', 'address' => '12 Sunder Nagar, Ahmedabad', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'pincode' => '380015', 'customer_type' => 'residential'],
            ['customer_code' => 'CUST-RES-1002', 'name' => 'Priya Sharma', 'email' => 'priya.sharma@gmail.com', 'phone' => '9876543202', 'address' => '45 Rose Garden, Surat', 'city' => 'Surat', 'state' => 'Gujarat', 'pincode' => '395001', 'customer_type' => 'residential'],
            ['customer_code' => 'CUST-COM-1003', 'name' => 'Mehta Industries', 'email' => 'accounts@mehtaind.com', 'phone' => '9876543203', 'address' => 'Plot 7, GIDC, Anand', 'city' => 'Anand', 'state' => 'Gujarat', 'pincode' => '388001', 'customer_type' => 'commercial'],
            ['customer_code' => 'CUST-RES-1004', 'name' => 'Suresh Patel', 'email' => 'suresh.patel@yahoo.com', 'phone' => '9876543204', 'address' => '8 Shanti Nagar, Vadodara', 'city' => 'Vadodara', 'state' => 'Gujarat', 'pincode' => '390001', 'customer_type' => 'residential'],
            ['customer_code' => 'CUST-IND-1005', 'name' => 'Parikh Textiles', 'email' => 'info@parikhtextiles.com', 'phone' => '9876543205', 'address' => 'Industrial Estate, Rajkot', 'city' => 'Rajkot', 'state' => 'Gujarat', 'pincode' => '360001', 'customer_type' => 'industrial'],
        ];
        $custIds = [];
        foreach ($customers as $c) {
            $cust = Customer::create($c);
            $custIds[] = $cust->id;
        }

        // Leads
        $statuses = ['new', 'contacted', 'follow_up', 'mature', 'converted'];
        $sources = ['website', 'referral', 'cold_call', 'social_media', 'exhibition'];
        $leadData = [
            ['name' => 'Amit Shah', 'email' => 'amit.shah@gmail.com', 'phone' => '9111111111', 'address' => 'Bopal, Ahmedabad', 'lead_source' => 'website', 'status' => 'new', 'estimated_value' => 185000, 'package_id' => $pkg1->id],
            ['name' => 'Neha Joshi', 'email' => 'neha.joshi@gmail.com', 'phone' => '9222222222', 'address' => 'Vesu, Surat', 'lead_source' => 'referral', 'status' => 'contacted', 'estimated_value' => 290000, 'package_id' => $pkg2->id],
            ['name' => 'Vinod Mehta', 'email' => 'vinod@mehtagroup.com', 'phone' => '9333333333', 'address' => 'Naroda, Ahmedabad', 'lead_source' => 'exhibition', 'status' => 'mature', 'estimated_value' => 550000, 'package_id' => $pkg3->id],
            ['name' => 'Sanjay Trivedi', 'email' => 'sanjay.trivedi@hotmail.com', 'phone' => '9444444444', 'address' => 'Manjalpur, Vadodara', 'lead_source' => 'cold_call', 'status' => 'follow_up', 'estimated_value' => 185000, 'package_id' => $pkg1->id],
            ['name' => 'Kavita Desai', 'email' => 'kavita.desai@gmail.com', 'phone' => '9555555555', 'address' => 'Katargam, Surat', 'lead_source' => 'social_media', 'status' => 'new', 'estimated_value' => 290000, 'package_id' => $pkg2->id],
        ];
        foreach ($leadData as $l) {
            Lead::create(array_merge($l, ['lead_number' => 'LEAD-' . date('Ymd') . '-' . rand(100, 999)]));
        }

        // Employees
        $employees = [
            ['employee_code' => 'EMP-SAL-001', 'name' => 'Rahul Verma', 'email' => 'rahul.v@solartech.com', 'phone' => '9600000001', 'department' => 'sales', 'designation' => 'Sales Executive', 'basic_salary' => 35000, 'joining_date' => '2022-01-15'],
            ['employee_code' => 'EMP-SAL-002', 'name' => 'Pooja Nair', 'email' => 'pooja.n@solartech.com', 'phone' => '9600000002', 'department' => 'sales', 'designation' => 'Senior Sales Executive', 'basic_salary' => 45000, 'joining_date' => '2021-06-01'],
            ['employee_code' => 'EMP-INS-001', 'name' => 'Deepak Singh', 'email' => 'deepak.s@solartech.com', 'phone' => '9600000003', 'department' => 'installation', 'designation' => 'Lead Technician', 'basic_salary' => 40000, 'joining_date' => '2021-03-10'],
            ['employee_code' => 'EMP-INS-002', 'name' => 'Ravi Kumar', 'email' => 'ravi.k@solartech.com', 'phone' => '9600000004', 'department' => 'installation', 'designation' => 'Solar Technician', 'basic_salary' => 28000, 'joining_date' => '2022-08-20'],
            ['employee_code' => 'EMP-ADM-001', 'name' => 'Anita Patel', 'email' => 'anita.p@solartech.com', 'phone' => '9600000005', 'department' => 'admin', 'designation' => 'Office Manager', 'basic_salary' => 38000, 'joining_date' => '2020-11-05'],
        ];
        $empIds = [];
        foreach ($employees as $e) {
            $emp = Employee::create(array_merge($e, ['is_active' => true]));
            $empIds[] = $emp->id;
            SalaryRecord::create(['employee_id' => $emp->id, 'month' => date('m'), 'year' => date('Y'), 'basic_salary' => $emp->basic_salary, 'allowances' => 5000, 'deductions' => 1500, 'net_salary' => $emp->basic_salary + 5000 - 1500, 'payment_date' => date('Y-m-d'), 'payment_mode' => 'bank_transfer', 'status' => 'paid']);
        }

        // Sales Orders
        $so1 = SalesOrder::create(['order_number' => 'SO-20240101-001', 'customer_id' => $custIds[0], 'customer_name' => 'Rajesh Kumar', 'customer_email' => 'rajesh@gmail.com', 'customer_phone' => '9876543201', 'customer_address' => '12 Sunder Nagar, Ahmedabad', 'total_amount' => 185000, 'tax_amount' => 9250, 'discount_amount' => 5000, 'final_amount' => 189250, 'status' => 'completed', 'payment_status' => 'paid']);
        SalesOrderItem::create(['sales_order_id' => $so1->id, 'product_id' => $productIds[0], 'description' => '6x Luminous 500W Mono PERC Panel', 'quantity' => 6, 'unit_price' => 15000, 'total_price' => 90000]);
        SalesOrderItem::create(['sales_order_id' => $so1->id, 'product_id' => $productIds[2], 'description' => 'Growatt 3kW Hybrid Inverter', 'quantity' => 1, 'unit_price' => 35000, 'total_price' => 35000]);
        SalesOrderItem::create(['sales_order_id' => $so1->id, 'product_id' => $productIds[5], 'description' => 'GI Mounting Structure', 'quantity' => 1, 'unit_price' => 11000, 'total_price' => 11000]);

        $so2 = SalesOrder::create(['order_number' => 'SO-20240201-002', 'customer_id' => $custIds[2], 'customer_name' => 'Mehta Industries', 'customer_email' => 'accounts@mehtaind.com', 'customer_phone' => '9876543203', 'customer_address' => 'Plot 7, GIDC, Anand', 'total_amount' => 550000, 'tax_amount' => 27500, 'discount_amount' => 15000, 'final_amount' => 562500, 'status' => 'processing', 'payment_status' => 'partial']);

        // Purchase Orders
        $po1 = PurchaseOrder::create(['po_number' => 'PO-20240101-001', 'supplier_name' => 'Waaree Energies Ltd', 'supplier_email' => 'supply@waaree.com', 'supplier_phone' => '9800000001', 'total_amount' => 140000, 'tax_amount' => 7000, 'final_amount' => 147000, 'status' => 'received', 'expected_delivery' => '2024-01-15', 'received_date' => '2024-01-14']);
        PurchaseOrderItem::create(['purchase_order_id' => $po1->id, 'product_id' => $productIds[1], 'description' => 'Waaree 550W Bi-Facial Panels x10', 'quantity' => 10, 'unit_price' => 14000, 'total_price' => 140000]);

        // Installations
        $inst1 = Installation::create(['installation_number' => 'INST-20240101-001', 'customer_id' => $custIds[0], 'sales_order_id' => $so1->id, 'scheduled_date' => '2024-01-20', 'completion_date' => '2024-01-22', 'system_size_kw' => 3, 'installation_address' => '12 Sunder Nagar, Ahmedabad', 'roof_type' => 'RCC Flat Roof', 'assigned_team' => 'Team Alpha', 'status' => 'completed']);

        // Service Requests
        ServiceRequest::create(['ticket_number' => 'SRV-20240301-001', 'customer_id' => $custIds[0], 'installation_id' => $inst1->id, 'service_type' => 'maintenance', 'priority' => 'medium', 'status' => 'open', 'description' => 'Annual maintenance check required. Customer reporting slight dip in generation.', 'scheduled_date' => date('Y-m-d', strtotime('+7 days'))]);
        ServiceRequest::create(['ticket_number' => 'SRV-20240302-002', 'customer_id' => $custIds[1], 'service_type' => 'inspection', 'priority' => 'high', 'status' => 'in_progress', 'description' => 'Inverter showing error code. Needs immediate inspection.', 'scheduled_date' => date('Y-m-d', strtotime('+2 days'))]);

        // Notifications
        Notification::create(['title' => 'Welcome to Solar ERP', 'message' => 'System initialized successfully. Admin panel is ready.', 'type' => 'general']);
        Notification::create(['title' => 'Low Stock Alert', 'message' => 'MC4 Solar Cable stock is below minimum level.', 'type' => 'inventory']);
        Notification::create(['title' => 'New Lead Received', 'message' => 'New inquiry from website - Kavita Desai wants 5kW system quote.', 'type' => 'lead']);
    }
}