<?php

return [

    'fantaholic' => [
        // CDN 주소
        'cdn' => 'https://bts-cdn.azureedge.net',
        // FCM 서버키
        'fcm_server_key'   => 'AAAAV7knT5g:APA91bGK1YlgsXe1Qt5jGTFRHPh_2fufOo45rzdGN9CYm_CRIVz3meEKjkkUbVaNqJJ4_pFBRNizW_lp9ub5OuuoGFU8yAfanEXVOHjyRdZjZhbuPgxynhW6md_mnKiOWpN8FTe8WjH3',
        //
        'curl_url' => 'http://devtrs.ce1eb.com',
        //구글 마켓 공유
        'shared_url'    => 'https://play.google.com/store/apps/details?id=com.celeb.tube.krieshachu',

        'app_name' => 'fantaholic',        //앱 이름

        'celeb_name' => 'fantaholic',         //셀럽 이름 => 크리샤 츄 회원등록이후 수정
        'artist_name'    => '',
        'youtube_channel' => 'UCEOA5dqY5Qzz5MaKBImavYg',
        //WECHAT
        'wechat_app_id' =>  env('KRIESHACHU_WECHAT_APP_ID'),
        'wechat_secret' =>  env('KRIESHACHU_WECHAT_SECRET'),
        'inspection'    =>  false
    ],
];
