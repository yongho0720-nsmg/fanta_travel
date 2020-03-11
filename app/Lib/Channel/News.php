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
//        $artist_id = 2;
        $names = \DB::table('artists')->where('id',$artist_id)->get();
        $client_id = "QI4CBOw2COVcXoMmVb0_";
        $client_secret = "XRgjR9vD0M";
        $encText = urlencode($names[0]->name);
        $url = "https://openapi.naver.com/v1/search/news.json?query=".$encText."&display=1&sort=sim"; // json 결과

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
//            dd($array_data['items']);

            $user = env('APP_NAME');

            if ($user != null) {
                $params['app'] = env('APP_NAME');
            } else {
                $params['app'] = 'fantaholic';
            }
            $cnt = 0;
            //dd($array_data['items']);
            foreach ($array_data['items'] as $item) {
                $dupleChk = $this->isValidation($item);
                if ($dupleChk > 0) {
                    break;
                }
//                $text = $item['description'];
//                dd($names[0]->name);
//                $reg = preg_match_all('/'. $names[0]->name . '/', $text, $matches);
//                dd($matches , $item);
                $search = 'naver';
                if(strpos($item['link'], $search)) {
                    $document = [
                        'artists_id' => $artist_id,
                        'app' => env('APP_NAME'),
                        'type' => $this->channelType,
                        'post' => preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $item['link']),
                        'post_type' => 'image',
                        'title' => strip_tags($item['title']),
                        'contents' => strip_tags($item['description']),
                        'recorded_at' => strftime("%Y-%m-%d %H:%M:%S", strtotime($item['pubDate'])),
                        'state' => 0,
                    ];


                    $html = $this->file_get_contents_curl(preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $item['link']));

                    $doc = new \DOMDocument();
                    @$doc->loadHTML($html);

                    $metas = $doc->getElementsByTagName('meta');

                    $img_url = "";
                    for ($i = 0; $i < $metas->length; $i++)
                    {
                        $meta = $metas->item($i);
                        if($meta->getAttribute('property') == 'og:image') {

                            $img_url = $meta->getAttribute('content');

                            // file save
                            if($img_url !== null) {
                                $util = new Util();
                                $resized_image = $util->AzureUploadImage($img_url, $this->channelImagePath);
                                //dd($resized_image['fileName']);
                                if($resized_image['fileName'] !== null) {
                                    $document['thumbnail_url'] = '/' . $this->channelImagePath . '/' . $resized_image['fileName'];
                                    $document['thumbnail_w'] = $resized_image['width'];
                                    $document['thumbnail_h'] = $resized_image['height'];
                                    $document['ori_thumbnail'] = preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $img_url);
                                    $document['data'] = array(['image' => $document['thumbnail_url']]);
                                    $document['ori_data'] = array($document['thumbnail_url']);
                                }

                            }
                        }
                    }



                    $board = Board::create($document);
                }
            } //for문
        }
    }

    private function parsingPost( $channelModa): string
    {
        $link = str_replace('https', 'http', $channelModa['link']);
        return $link;
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
            $board->title = strip_tags($array_data['title']);
            $board->contents = strip_tags($array_data['description']);
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
        $chk = Board::where('post', '=', $this->parsingPost($channelModel))->count();
        return $chk;
        // TODO: Implement isValidation() method.
    }

    public function getChannelContentsAll()
    {
        $artist_id = $this->artistsId;
//        $artist_id = 2;
        $names = \DB::table('artists')->where('id',$artist_id)->get();
        $client_id = "QI4CBOw2COVcXoMmVb0_";
        $client_secret = "XRgjR9vD0M";
        $encText = urlencode($names[0]->name);
        $url = "https://openapi.naver.com/v1/search/news.json?query=".$encText."&display=1"; // json 결과

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
//            dd($array_data['items']);

            $user = env('APP_NAME');

            if ($user != null) {
                $params['app'] = env('APP_NAME');
            } else {
                $params['app'] = 'fantaholic';
            }
            $cnt = 0;
            //dd($array_data['items']);
            foreach ($array_data['items'] as $item) {
                $dupleChk = $this->isValidation($item);
                $search = 'naver';
                if ($dupleChk > 0) {
                    break;
                }
                if(strpos($item['link'], $search)) {
                    $document = [
                        'artists_id' => $artist_id,
                        'app' => env('APP_NAME'),
                        'type' => $this->channelType,
                        'post' => preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $item['link']),
                        'post_type' => 'image',
                        'title' => strip_tags($item['title']),
                        'contents' => strip_tags($item['description']),
                        'recorded_at' => strftime("%Y-%m-%d %H:%M:%S", strtotime($item['pubDate'])),
                        'state' => 0,
                    ];

                    $html = $this->file_get_contents_curl(preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $item['link']));

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
                    // file save
                    if($img_url !== null) {
                        $util = new Util();
                        $resized_image = $util->AzureUploadImage($img_url, $this->channelImagePath);

                        if($resized_image['fileName'] !== null) {
                            $document['thumbnail_url'] = '/' . $this->channelImagePath . '/' . $resized_image['fileName'];
                            $document['thumbnail_w'] = $resized_image['width'];
                            $document['thumbnail_h'] = $resized_image['height'];
                            $document['ori_thumbnail'] = preg_match('#^http:#', $url) ? $url : str_replace('https:', 'http:', $img_url);
                            $document['data'] = array(['image' => $document['thumbnail_url']]);
                            $document['ori_data'] = array($document['thumbnail_url']);
                        }

                    }



                    $board = Board::create($document);
                }
            } //for문
        }
    }

}
