<?php

namespace App\Http\Controllers;

use App\Events\MessagePosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class MessagesController extends Controller
{
    public $redis;

    public function __construct()
    {
        $this->redis = Redis::connection();
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
            $arr[] = json_decode($msn[$i]);
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

        $message = $user->messages()->create([
            'message' => $request['message']
        ]);

        /*$msn = $message->join('users', 'users.id', 'messages.user_id')
            ->select('messages.message','users.name')
            ->get();*/

        $msn = [
            'message' => $message->message,
            'name' => $user->name
        ];
        $this->redis->hmset("messages", ['message'.$message->id => json_encode($msn)]);

        broadcast(new MessagePosted($msn, $message->id))->toOthers();

        return ['status' => 'OK'];
    }
}
