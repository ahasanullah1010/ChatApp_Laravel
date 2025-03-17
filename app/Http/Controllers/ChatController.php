<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // ওয়ান-টু-ওয়ান চ্যাট দেখানো
    public function index($receiver_id)
    {
        // $receiver = User::findOrFail($receiver_id);
        // $messages = Chat::where(function ($query) use ($receiver_id) {
        //     $query->where('sender_id', Auth::id())->where('receiver_id', $receiver_id);
        // })->orWhere(function ($query) use ($receiver_id) {
        //     $query->where('sender_id', $receiver_id)->where('receiver_id', Auth::id());
        // })->orderBy('created_at', 'asc')->get();



        // $userId = Auth::id();
        // $connections = Connection::where('sender_id', $userId)
        //     ->orWhere('receiver_id', $userId)
        //     ->orderBy('last_message_time', 'desc')
        //     ->get();

        // return view('chat.dashboard', compact('receiver', 'messages', 'connections'));


        try {
            // Receiver user খুঁজে বের করা
            $receiver = User::findOrFail($receiver_id);
    
            // লগইনকৃত ব্যবহারকারীর আইডি
            $userId = Auth::id();
    
            // User & Receiver-এর মধ্যে মেসেজ খোঁজা
            $messages = Chat::where(function ($query) use ($receiver_id) {
                $query->where('sender_id', Auth::id())->where('receiver_id', $receiver_id);
            })->orWhere(function ($query) use ($receiver_id) {
                $query->where('sender_id', $receiver_id)->where('receiver_id', Auth::id());
            })->orderBy('created_at', 'asc')->get();
    
            // ব্যবহারকারীর সংযোগ খোঁজা
            $connections = Connection::where('sender_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->orderBy('last_message_time', 'desc')
                ->get();
    
            // JSON Response রিটার্ন করা
            return response()->json([
                'status' => 'success',
                'receiver' => $receiver,
                'messages' => $messages,
                'connections' => $connections
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Receiver not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }









    public function conn()
    {
        
        try {
            
            // লগইনকৃত ব্যবহারকারীর আইডি
            $userId = Auth::id();
    
           
    
            // ব্যবহারকারীর সংযোগ খোঁজা
            $connections = Connection::where('sender_id', $userId)
                ->orWhere('receiver_id', $userId)
                ->orderBy('last_message_time', 'desc')
                ->with(['sender', 'receiver']) // Load sender & receiver details
                ->get();
    
            // JSON Response রিটার্ন করা
            return response()->json([
                'status' => 'success',
            
                'connections' => $connections
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }









    // নতুন মেসেজ সেন্ড করা
    public function sendMessage(Request $request, $receiver_id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver_id,
            'message' => $request->message,
        ]);

        // Connection টেবিল আপডেট করুন
        Connection::updateOrCreate(
            [
                'sender_id' => Auth::id(),
                'receiver_id' => $receiver_id,
            ],
            [
                'last_message' => $request->message,
                'last_message_time' => now(),
            ]
        );

        return back();
    }




    // message send api
    public function send(Request $request, $receiver_id)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiver_id,
            'message' => $request->message
        ]);

        // Connection টেবিল আপডেট করুন
        // লগইনকৃত ইউজারের আইডি
        $authUserId = Auth::id();

        // বিদ্যমান সংযোগ খোঁজা (sender_id বা receiver_id যেকোনো একটিতে বর্তমান ইউজার আছে কিনা)
        $connection = Connection::where(function ($query) use ($authUserId, $receiver_id) {
                $query->where('sender_id', $authUserId)
                    ->where('receiver_id', $receiver_id);
            })
            ->orWhere(function ($query) use ($authUserId, $receiver_id) {
                $query->where('sender_id', $receiver_id)
                    ->where('receiver_id', $authUserId);
            })
            ->first();

        if ($connection) {
            // যদি Connection আগে থেকেই থাকে, তাহলে শুধু আপডেট করো
            $connection->update([
                'last_message' => $request->message,
                'last_message_time' => now(),
            ]);
        } else {
            // যদি Connection না থাকে, তাহলে নতুন Connection তৈরি করো
            Connection::create([
                'sender_id' => $authUserId,
                'receiver_id' => $receiver_id,
                'last_message' => $request->message,
                'last_message_time' => now(),
            ]);
        }


        return response()->json([
            'status' => 'success',
            'message' => $message
        ]);
    }

}
