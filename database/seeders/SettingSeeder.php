<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $settings = [
            // [
            //     'key'    => 'site_logo',
            //     'value'  => null,
            //     'type'   => 'image',
            //     'details' => null,
            //     'display_name'=>'Site Logo',
            //     'group'  => 'web',
            //     'status' => 1,
            //     'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            //     'created_by' => 1,
            // ],
            // [
            //     'key'    => 'favicon',
            //     'value'  => null,
            //     'type'   => 'image',
            //     'details' => null,
            //     'display_name'=>'Favicon',
            //     'group'  => 'web',
            //     'status' => 1,
            //     'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            //     'created_by' => 1,
            // ],
            [

                'key'    => 'company_name',
                'value'  => 'Invoice',
                'type'   => 'text',
                'display_name'  => 'Company Name [Login Page,Sidebar Top Title]',
                'group'  => 'web',
                'details' => null,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [

                'key'    => 'company_email',
                'value'  => 'khushisoni.hipl@gmail.com',
                'type'   => 'text',
                'display_name'  => 'Company Email [Receive DB Backup]',
                'group'  => 'web',
                'details' => null,
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [
                'key'    => 'share_invoice_mail_message',
                'value'  => 'Dear [PARTY_NAME] , Please Find your Invoice Detail below.',
                'type'   => 'text_area',
                'details' => '[PARTY_NAME], [SUPPORT_EMAIL], [SUPPORT_PHONE], [APP_NAME]',
                'display_name'=>'Share-Invoice Mail Message',
                'group'  => 'web',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [
                'key'    => 'share_invoice_whatsapp_message',
                'value'  =>  'Dear Customer , Please Find your Invoice Detail below.',
                'type'   => 'text',
                'details' => null,
                'display_name'=>'Share-Invoice Whatsapp Message',
                'group'  => 'web',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [
                'key'    => 'custom_invoice_print_message',
                'value'  =>  'Hello Dear, Please check your invoice Details. If there is any query , kindly contact with us.',
                'type'   => 'text',
                'details' => null,
                'display_name'=>'Custom Print-Invoice Bottom Message',
                'group'  => 'web',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [
                'key'    => 'invoice_pdf_top_title',
                'value'  =>  'Kanak Bangles',
                'type'   => 'text',
                'details' => '20',
                'display_name'=>'Invoice Pdf Top Title',
                'group'  => 'web',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
            [
                'key'    => 'invoice_allow_day_admin_accountant',
                'value'  =>  7,
                'type'   => 'number',
                'details' => null,
                'display_name'=>'Invoice access allow Days for admin & accountent role',
                'group'  => 'web',
                'status' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'created_by' => 1,
            ],
        ];

        Setting::insert($settings);
    }
}
