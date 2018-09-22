<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessagePosted;
use Illuminate\Support\Facades\Redis;

class MessagesController extends Controller
{
    public $redis;
    public $imageLink;
    public $imageLinkValue;

    public function __construct()
    {
        $this->redis = Redis::connection();
        $this->imageLink = false;
        $this->imageLinkValue = false;
    }
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $msn = $this->redis->hvals('messages');
        $arr = array();

        for ($i = 0 ; $i < count($msn) ; $i++){
            if (is_object(json_decode(json_decode($msn[$i])->message))){
                $obj = json_decode($msn[$i]);
                $obj->message = json_decode(json_decode($msn[$i])->message);
                $arr[] = $obj;
            }else{
                $arr[] = json_decode($msn[$i]);
            }
        }
        return $arr;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (is_array($request['message'])) {
            $this->imageLink = true;
            $this->imageLinkValue = $request['message'];
            $messageText = json_encode($request['message']);
        }else{
            $messageText = $request['message'];
        }

        $message = $user->messages()->create([
            'message' => $messageText
        ]);

        $msn = [
            'message' => $messageText,
            'name' => $user->name,
            'imageLink' => $this->imageLink
        ];

        $this->redis->hmset("messages", ['message'.$message->id => json_encode($msn)]);

        broadcast(new MessagePosted($msn, $message->id, $this->imageLinkValue))->toOthers();

        return ['status' => 'OK'];
    }

    public function file(Request $request)
    {
        if ($request->hasFile('file')) {
            $url = $request->file->store('messages/file');
            auth()->user()->messages()->create([
                'message' => $url
            ]);
            return ['imageLink' => $url];
        }
        return ['status' => 'ERROR, image not found.'];
    }
}
