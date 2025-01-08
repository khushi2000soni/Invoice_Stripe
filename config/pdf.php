<?php

return [
    'mode'                     => '',
    'format'                   => 'A4',
    'default_font_size'        => '12',
    'default_font'             => 'sans-serif',
    'margin_left'              => 10,
    'margin_right'             => 10,
    'margin_top'               => 10,
    'margin_bottom'            => 10,
    'margin_header'            => 0,
    'margin_footer'            => 0,
    'orientation'              => 'P',
    'title'                    => 'Invoice Pdf',
    'subject'                  => '',
    'author'                   => '',
    'watermark'                => '',
    'show_watermark'           => false,
    'show_watermark_image'     => false,
    'auto_page_break' => true,
    'watermark_font'           => 'sans-serif',
    'display_mode'             => 'fullpage',
    'watermark_text_alpha'     => 0.1,
    'watermark_image_path'     => public_path("admintheme/assets/img/cancelled.png"),
    'watermark_image_alpha'    => 0.4,
    'watermark_image_size'     => 'D',
    'watermark_image_position' => 'C',
    'custom_font_dir'          => storage_path('fonts/'),
    'custom_font_data'         => [
        'Mangal' => [ // must be lowercase and snake_case
            'B'  => 'Mangal-Bold.ttf',    // regular font
                  // optional: bold font
        ]
    ],
    'auto_language_detection'  => false,
    'temp_dir'                 => storage_path('app'),
    'pdfa'                     => false,
    'pdfaauto'                 => false,
    'use_active_forms'         => false,
];
