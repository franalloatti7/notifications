<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\User;
use App\Notifications\NotifyNotification;
use Illuminate\Http\RedirectResponse;
use Centrifugo\Centrifugo;
use Illuminate\Http\Request;
use Illuminate\Database\Query\JoinClause;
use Pusher\Pusher;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function publish(StoreEventRequest $request)
    {
        $options = [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'encrypted' => true
        ];
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );
        $data['message'] = $request->message;
        $data['tag'] = $request->tag;
        $data['created_at'] = new \DateTime();

        $users = DB::table('tag_user')
        ->join('tags', 'tags.id', '=', 'tag_user.tag_id')
        ->where('tags.name',$request->tag)->get();

        foreach($users as $user){
            $the_user = User::where('id', $user->user_id)->get()->first();
            $the_user->notify(new NotifyNotification($request->tag, $request->message));
        }

        $pusher->trigger($request->tag, 'App\\Events\\NotifyEvent', $data);

        return json_encode(['message' => 'Publicación realizada con éxito']);
    }

    public function markread($id){
        auth()->user()->unreadNotifications->when($id, function($query) use ($id){
            return $query->where('id', $id);
        })->markAsRead();
        return json_encode(['message' => 'La notificación fue marcada como leída']);
    }

    public function getMyNotifications(){
        $readNotifications = auth()->user()->readNotifications->take(5);
        foreach($readNotifications as $notification){
            $notification->createdAt = $notification->created_at->diffForHumans();
        }
        $unReadNotifications = auth()->user()->unreadNotifications->take(5);
        foreach($unReadNotifications as $notification){
            $notification->createdAt = $notification->created_at->diffForHumans();
        }
        return json_encode(['read' => $readNotifications, 'unread' => $unReadNotifications]);
    }

    public function history(Request $request){
        $fields = $request->all();
        $notifications = DB::table('notifications')->where('notifiable_id',auth()->user()->id)->orderBy('created_at', 'desc');
        if(isset($fields['search'])){
            $notifications = $notifications->where('data', 'like', '%'.$fields['search'].'%');
        }
        $notifications = $notifications->paginate(20);
        return view('notifications.history', compact('notifications'));
    }
}
