<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // role_create ,role_edit,role_show,  role_access profile_access profile_edit user_change_password address_access address_create address_edit address_delete staff_access staff_create staff_edit staff_delete customer_access customer_create customer_edit customer_delete master_access category_access category_create category_edit category_delete product_access product_create product_edit product_delete  device_access device_create device_edit device_delete invoice_access invoice_create invoice_edit invoice_show invoice_print invoice_share invoice_download invoice_delete invoice_restore order_product_access order_product_create order_product_copy order_product_delete report_access get_sales_report_access setting_access  setting_edit product_merge product_print product_export category_print category_export address_print address_export report_category_access phone_book_access phone_book_print phone_book_export invoice_recycle_access customer_print customer_export staff_print staff_export invoice_export invoice_filter dashboard_widget_access invoice_date_filter setting_invoice_allow_days modified_customer_access , modified_customer_approve ,modified_customer_edit ,modified_product_access,modified_product_approve,modified_product_edit backup_access backup_create backup_restore backup_delete staff_rejoin backup_download backup_upload

        $roles = Role::all();
        $superadminpermissionid= Permission::all();

        $adminpermissionid= Permission::whereIn('name',['profile_access', 'profile_edit', 'user_change_password' ,'invoice_access','invoice_filter','invoice_create','invoice_delete','invoice_edit','invoice_show','invoice_print','invoice_export', 'invoice_share' ,'invoice_download', 'order_product_access', 'order_product_create', 'order_product_copy' ,'order_product_delete' ,'phone_book_access','phone_book_print','phone_book_export','customer_edit','customer_delete','customer_create','customer_print','customer_export'])->pluck('id')->toArray();

        $accountantpermissionid= Permission::whereIn('name',['profile_access', 'profile_edit', 'user_change_password' ,
        'staff_access','staff_create','staff_edit','staff_delete','staff_print','staff_export','staff_rejoin','invoice_access','invoice_filter','invoice_create','invoice_delete','invoice_edit','invoice_show','invoice_print','invoice_export','invoice_share' ,'invoice_download','invoice_restore','invoice_recycle_access', 'order_product_access', 'order_product_create', 'order_product_copy' ,'order_product_delete' ,'customer_access','customer_edit','customer_delete','customer_create','customer_print','customer_export','address_access','address_edit','address_delete','address_print','address_export','master_access','category_access','category_create','category_edit','category_delete','category_print','category_export','product_access','product_create','product_edit','product_delete','product_merge','product_print','product_export','device_access','device_create','device_edit','device_delete','setting_access','setting_edit','phone_book_access','phone_book_print','phone_book_export'])->pluck('id')->toArray();

        $staffpermissionid= Permission::whereIn('name',['profile_access'])->pluck('id')->toArray();

        foreach ($roles as $role) {
            switch ($role->id) {
                case 1:
                    $role->givePermissionTo($superadminpermissionid);
                    break;
                case 2:
                    $role->givePermissionTo($adminpermissionid);
                    break;
                case 3:
                    $role->givePermissionTo($accountantpermissionid);
                    break;
                case 4:
                    $role->givePermissionTo($staffpermissionid);
                    break;
                default:
                    break;
            }
        }
    }
}
