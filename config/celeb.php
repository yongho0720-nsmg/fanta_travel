<?php

return [

    'krieshachu' => [
        // CDN 주소
        'cdn' => 'https://krieshachu-cdn.azureedge.net',
        // FCM 서버키
        'fcm_server_key'   => 'AAAATlQrl4U:APA91bF51Cx_YF6ipiNwTPFn5Bq5BTgl0AoFromO6jPULW7697nwkloaucy4ZOGibie-MsoQcTU_4y7JTzdPJGXopyqbMUHNzDa07uWFupRVDWitngyd2u0jpl48o_bZlpguzhC3vA06',
        //
        'curl_url' => 'http://devtrs.ce1eb.com',
        //구글 마켓 공유
        'shared_url'    => 'https://play.google.com/store/apps/details?id=com.celeb.tube.krieshachu',

        'app_name' => 'krieshachu',        //앱 이름

        'celeb_name' => '크리샤 츄',         //셀럽 이름 => 크리샤 츄 회원등록이후 수정
        'artist_name'    => '크리샤 츄',
        'youtube_channel' => 'UCEOA5dqY5Qzz5MaKBImavYg',
        //WECHAT
        'wechat_app_id' =>  env('KRIESHACHU_WECHAT_APP_ID'),
        'wechat_secret' =>  env('KRIESHACHU_WECHAT_SECRET'),
        'inspection'    =>  false
    ],

    'leeseol' => [
        'instagram' => [
            'access_token' => '2289720332.925213c.9c049f9c43314db7a5a80789f62c40ec'
        ],
        'version' => [
            'and' => [
                'app_version' => '1.2.5',
                'is_forced' => 1
            ],
            'ios' => [
                'app_version' => '0.0.1',
                'is_forced' => 0
            ]
        ],
        'package_name'   =>  'com.tube.celeb.leeseol.v2',
        'service_start_date' => '2019-06-30 00:00:00',
        'cdn' => 'http://cdn.ce1eb.com',
        'curl_url' => 'https://trs.ce1eb.com',
        'app_name' => '이설튜브',
        'celeb_name' => '이설',
        'account'   =>  'leeseol00',
        'media_idx' => 273

    ],


    'bts' => [
        // CDN 주소
        'cdn' => 'https://bts-cdn.azureedge.net',
        // FCM 서버키
        'fcm_server_key'   => 'AAAAV7knT5g:APA91bGK1YlgsXe1Qt5jGTFRHPh_2fufOo45rzdGN9CYm_CRIVz3meEKjkkUbVaNqJJ4_pFBRNizW_lp9ub5OuuoGFU8yAfanEXVOHjyRdZjZhbuPgxynhW6md_mnKiOWpN8FTe8WjH3',
        //
        'curl_url' => 'http://devtrs.ce1eb.com',
        //구글 마켓 공유
        'shared_url'    => 'https://play.google.com/store/apps/details?id=com.celeb.tube.krieshachu',

        'app_name' => 'bts',        //앱 이름

        'celeb_name' => 'BTS',         //셀럽 이름 => 크리샤 츄 회원등록이후 수정
        'artist_name'    => '크리샤 츄',
        'youtube_channel' => 'UCEOA5dqY5Qzz5MaKBImavYg',
        //WECHAT
        'wechat_app_id' =>  env('KRIESHACHU_WECHAT_APP_ID'),
        'wechat_secret' =>  env('KRIESHACHU_WECHAT_SECRET'),
        'inspection'    =>  false
    ],

//    // 지훈 튜브
//   'jihoon' => [
//        'cdn' => 'http://cdn.ce1eb.com',
//        'fcm_server_key'   => 'AAAAvM-bt7Q:APA91bHhGCkRhZ3GOC1eegjvww6hLUpZhfdz9c8YwqMoTR_BIJrrB06Wr8azPJUzl6AilfsmlDtQK5wjFrJMABW0jgj4V1BKB6Tq4vT_Cn5bFwEfKVJoHqZr508ZVfUurG9glnFcX6H1',
//        'curl_url' => 'http://devtrs.ce1eb.com',
//        'app_name' => 'Jihoon',
//        'celeb_name' => 'Jihoon',
//        'artist_name'    => '박지훈',
//        'inspection'    =>  false,
//    ],
//
//    // Pinxy
//    'pinxy' => [
//        'cdn' => 'http://cdn.ce1eb.com',
//        'shared_url'    =>'https://play.google.com/store/apps/details?id=com.tube.online.pinxy',
//        'fcm_server_key'   => '',
//        'curl_url' => 'http://devtrs.ce1eb.com',
//        'app_name' => 'Pinxy',
//        'celeb_name' => 'Pinxy',
//        'inspection'    =>  false
//    ]
];
