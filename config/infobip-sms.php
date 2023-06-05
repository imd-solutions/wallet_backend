<?php

/**
 * This is config for Infobip SMS.
 *
 * @see https://dev.infobip.com/send-sms/single-sms-message
 */
return [
    'from'     => env('INFOBIP_FROM', 'Laravel'),
    'username' => env('INFOBIP_USERNAME', 'user'),
    'password' => env('INFOBIP_PASSWORD', '123456'),
];

/*
 *
 * Configuration::query()->firstOrCreate(
            ['name' => 'infobip_api_url'],
            [
                'group_id' => 2,
                'value'    => 'https://xnkz3.api.infobip.com',
                'type'     => 'string',
                'options'  => null,
            ]
        );

        Configuration::query()->firstOrCreate(
            ['name' => 'infobip_api_key'],
            [
                'group_id' => 2,
                'value'    => 'ec8ce6a68ed9d5652f13f8d0d986548b-285a062e-beab-4f0f-8674-88850d6da552',
                'type'     => 'string',
                'options'  => null,
            ]
        );

        Configuration::query()->firstOrCreate(
            ['name' => 'infobip_api_app_id'],
            [
                'group_id' => 2,
                'value'    => '7EBA612DC99973D55347E7AB92A717E6',
                'type'     => 'string',
                'options'  => null,
            ]
        );
 *
 * */
