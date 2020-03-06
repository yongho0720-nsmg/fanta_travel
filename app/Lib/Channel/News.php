<?php

namespace App\Lib\Channel;

use Alaouy\Youtube\Facades\Youtube as YoutubeApi;
use App\Board;
use App\Lib\Channel\Factory\ChannelAbstractClass;
use App\Lib\Channel\Factory\stdClass;
use App\Lib\Util;
use Illuminate\Support\Facades\Log;

class News extends ChannelAbstractClass
{

    private $artistsId;
    private $channelType = 'news';
    private $channelImagePath = 'images/news/thumbnail';

    public function __construct($artistsId){
        $this->artistsId = $artistsId;
    }


    public function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function getChannelContents()
    {
        $artist_id = $this->artistsId;
        $names = \DB::table('artists')->where('id',$artist_id)->get();
        $client_id = "QI4CBOw2COVcXoMmVb0_";
        $client_secret = "XRgjR9vD0M";
        $encText = urlencode($names[0]->name);
        $url = "https://openapi.naver.com/v1/search/news.json?query=".$encText; // json 결과

        $is_post = false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array();
        $headers[] = "X-Naver-Client-Id: ".$client_id;
        $headers[] = "X-Naver-Client-Secret: ".$client_secret;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "status_code:".$status_code."";
        curl_close ($ch);

        if($status_code == 200) {
            $array_data = json_decode($response, true);
//            print_r($array_data['items']);

            $user = env('APP_NAME');

            if ($user != null) {
                $params['app'] = env('APP_NAME');
            } else {
                $params['app'] = 'fantaholic';
            }
            $cnt = 0;
            foreach ($array_data['items'] as $item) {
//                $dupleChk = $this->isValidation($item);
//                $cnt++;
//                if ($dupleChk) {
//                    continue;
//                }
                $document = [
                    'artists_id' => $artist_id,
                    'app' => env('APP_NAME'),
                    'type' => $this->channelType,
                    'post' => preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $item['originallink']),
                    'post_type' => 'image',
                    'title' => $item['title'],
                    'contents' => $item['description'],
                    'recorded_at' => strftime("%Y-%m-%d %H:%M:%S", strtotime($item['pubDate'])),
                    'state' => 1,
                ];
            } //for문
            $html = $this->file_get_contents_curl($item['originallink']);

            $doc = new \DOMDocument();
            @$doc->loadHTML($html);

            $metas = $doc->getElementsByTagName('meta');

            $img_url = "";
            for ($i = 0; $i < $metas->length; $i++)
            {
                $meta = $metas->item($i);
                if($meta->getAttribute('property') == 'og:image')
                    $img_url = $meta->getAttribute('content');
            }
            $img_url = (preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $img_url));
            $document['ori_thumbnail'] = $img_url;

            // file save
            $util = new Util();
            $path = 'images/'.'news'.'/thumbnail/';
            $resized_image = $util->AzureUploadImage($img_url, $path);
            $image_save = new \stdClass();
            $image_save->image = "/".$path.$resized_image['fileName'];
            $data = [$image_save];
            $document['data'] = $data;
            $document['thumbnail_url'] = $image_save->image;
            $document['thumbnail_w'] = (int)$resized_image['width'];
            $document['thumbnail_h'] = (int)$resized_image['height'];
            $ori_data = [$resized_image['path']];
            $document['ori_data'] = $ori_data;


            $board = Board::create($document);

        }
    }

    private function parsingPost( $channelModa): string
    {
        return $channelModa->id->videoId;
//        dd($channelModa->id->videoId);
    }

    protected function setDataFormatting($channelMode)
    {
        $board = new Board();
        $util = new Util();
        Log::debug(__METHOD__ . ' - content - ' . json_encode($channelMode));

        $client_id = "QI4CBOw2COVcXoMmVb0_";
        $client_secret = "XRgjR9vD0M";
        $encText = $this->artistsId;
        $url = "https://openapi.naver.com/v1/search/news.json?query=".$encText; // json 결과

        $is_post = false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array();
        $headers[] = "X-Naver-Client-Id: ".$client_id;
        $headers[] = "X-Naver-Client-Secret: ".$client_secret;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "status_code:".$status_code."";
        curl_close ($ch);

        if($status_code == 200) {
            $array_data = json_decode($response, true);
            //echo $array_data;
            //print_r($array_data['items']);

            $board->app = env('APP_NAME');
            $board->type = $this->channelType;
            $board->post = $channelMode->id;
            $board->title = $array_data['title'];
            $board->contents = $array_data['description'];
            $board->ori_tag = [];
            $board->gender = 1;
            $board->state = 1;
            $board->created_at = date('Y-m-d H:i:s');
            $board->recorded_at = strftime("%Y-%m-%d %H:%M:%S", strtotime($array_data['pubDate']));


            $html = $this->file_get_contents_curl($array_data['originallink']);

            $doc = new \DOMDocument();
            @$doc->loadHTML($html);

            $metas = $doc->getElementsByTagName('meta');

            $img_url = "";
            for ($i = 0; $i < $metas->length; $i++)
            {
                $meta = $metas->item($i);
                if($meta->getAttribute('property') == 'og:image')
                    $img_url = $meta->getAttribute('content');
            }
            $thumbnail = $util->AzureUploadImage($img_url, $this->channelImagePath);
            $board->thumbnail_url = "/" . $this->channelImagePath . $thumbnail['fileName'];
            $board->thumbnail_w = (int)$thumbnail['width'];
            $board->thumbnail_h = (int)$thumbnail['height'];
            $board->ori_thumbnail = $img_url;
            $board->data = array(['image' => $board->thumbnail_url]);
            $board->ori_data = array($board->thumbnail_url);
            $board->post = $array_data['originallink'];
            $board->post_type = 'image';

            //$board = Board::create($document);
        } //if문
        return $board;
    }



    protected function isValidation( $channelModel)
    {
        return Board::where('post', '=', $this->parsingPost($channelModel))->count();
        // TODO: Implement isValidation() method.
    }

    public function getChannelContentsAll()
    {
        $artist_id = $this->artistsId;
        $names = \DB::table('artists')->where('id',$artist_id)->get();
        $client_id = "QI4CBOw2COVcXoMmVb0_";
        $client_secret = "XRgjR9vD0M";
        $encText = urlencode($names[0]->name);
        $url = "https://openapi.naver.com/v1/search/news.json?query=".$encText; // json 결과

        $is_post = false;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, $is_post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array();
        $headers[] = "X-Naver-Client-Id: ".$client_id;
        $headers[] = "X-Naver-Client-Secret: ".$client_secret;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec ($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "status_code:".$status_code."";
        curl_close ($ch);

        if($status_code == 200) {
            $array_data = json_decode($response, true);
//            print_r($array_data['items']);

            $user = env('APP_NAME');

            if ($user != null) {
                $params['app'] = env('APP_NAME');
            } else {
                $params['app'] = 'fantaholic';
            }
            foreach ($array_data['items'] as $item) {
                $document = [
                    'artists_id' => $artist_id,
                    'app' => env('APP_NAME'),
                    'type' => $this->channelType,
                    'post' => preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $item['originallink']),
                    'post_type' => 'image',
                    'title' => $item['title'],
                    'contents' => $item['description'],
                    'recorded_at' => strftime("%Y-%m-%d %H:%M:%S", strtotime($item['pubDate'])),
                    'state' => 1,
                ];
            } //for문
            $html = $this->file_get_contents_curl($item['originallink']);

            $doc = new \DOMDocument();
            @$doc->loadHTML($html);

            $metas = $doc->getElementsByTagName('meta');

            $img_url = "";
            for ($i = 0; $i < $metas->length; $i++)
            {
                $meta = $metas->item($i);
                if($meta->getAttribute('property') == 'og:image')
                    $img_url = $meta->getAttribute('content');
            }
            $img_url = (preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $img_url));
            $document['ori_thumbnail'] = $img_url;

            // file save
            $util = new Util();
            $path = 'images/'.'news'.'/thumbnail/';
            $resized_image = $util->AzureUploadImage($img_url, $path);
            //dd($resized_image['fileName']);
            $image_save = new \stdClass();
            $image_save->image = "/".$path.$resized_image['fileName'];
            $data = [$image_save];
            $document['data'] = $data;
            $document['thumbnail_url'] = $image_save->image;
            $document['thumbnail_w'] = (int)$resized_image['width'];
            $document['thumbnail_h'] = (int)$resized_image['height'];
            $ori_data = [$resized_image['path']];
            $document['ori_data'] = $ori_data;


            $board = Board::create($document);

        }
    }

}
