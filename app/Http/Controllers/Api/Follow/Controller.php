<?php

namespace App\Http\Controllers\Api\Follow;

use App\Board;
use App\Push;
use App\Lib\LobbyClassv6;
use App\Follow;
use App\Lib\Response;
use App\Lib\Util;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Lib\Log;
use App\Http\Controllers\Controller as baseController;


class Controller extends baseController
{
    protected $response;

    public function __construct()
    {
        $this->response = new Response();
    }
    
    //팔로우
    public function index(Request $request){

        $user = Auth('api')->user();

        if(!$request->input('artist_id')){
            return $this->response->set_response(-2001,null);
        }

        $artist_id_arr = explode(",",$request->input('artist_id'));

        Follow::where('user_id', '=', $user->id)->delete();// 팔로우 초기화

        foreach($artist_id_arr as $artist_id){
          $params = [
              'artist_id' =>  $artist_id,
              'user_id'  =>  $user->id
          ];

          $result = Follow::create($params); // 팔로우 등록
        }

        $data = [
            'follow'   => count($artist_id_arr)
        ];

        return $this->response->set_response(0,$data);

    }

    //언팔로우
    public function unfollow(Request $request){

        $user = Auth('api')->user();

        $params = [
            'artist_id' =>  $request->input('artist_id'),
            'user_id'  =>  $user->id
        ];

        if(!$params['artist_id']){
            return $this->response->set_response(-2001,null);
        }

        $result = Follow::where('artist_id', '=', $params['artist_id'])->where('user_id', '=', $params['user_id'])->delete();
        $data = array();
        if($result > 0){
          return $this->response->set_response(0,null);
        }else{
          return $this->response->set_response(-2001,null);
        }
    }


}
