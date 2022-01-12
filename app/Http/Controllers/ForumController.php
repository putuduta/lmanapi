<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Forum;
use App\Models\ForumReply;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class ForumController extends Controller
{

    public function index(Request $request) {
        $user = JWTAuth::authenticate($request->token);
        if ($user) {
            $messages = $this->getForumByCourse($request);

            return response()->json([
                'success' => true,
                'data' => $messages,
            ], Response::HTTP_OK);
        }

        return response()->json([
            'success' => false,
        ], 404);
    }

    public function getForumByCourse(Request $request) {
        $forums = DB::table('forums')
        ->join('schedules', 'schedules.id', '=', 'forums.schedule_id')
        ->join('courses', 'courses.id', '=', 'schedules.course_id')
        ->join('users', 'users.id', '=', 'forums.user_id')
        ->select(
            'courses.name as course_name',
            'forums.id as forum_id',
            'forums.title',
            'forums.description',
            'forums.file',
            'forums.created_at',
            'users.name as user_name',
            'users.email as user_email',
            'users.phone_number as user_phone_number',
        )
        ->where('courses.id', '=', $request->course_id)
        ->orderBy('forums.id', 'desc')
        ->get();

        return $forums;
    }

    public function store(Request $request) {
        $user = JWTAuth::authenticate($request->token);
        if ($user) {

            if ($request->hasFile('file')) {
                $extension = $request->file('file')->getClientOriginalExtension();
                $file_name = 'REPLY_' . $request->title . '_' . time() . '.' . $extension;
    
                $request->file('file')->storeAs('public/forum', $file_name);
            } else {
                $file_name = NULL;
            }

            Forum::create([
                'schedule_id' => $request->schedule_id,
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'file' => $file_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Forum Created'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'success' => false,
        ], 404);
    }

    
    public function getForumById(Request $request, $id) {
        $user = JWTAuth::authenticate($request->token);
        if ($user) {
            $forum = Forum::where('id', $id)->first();

            $forumReplies = DB::table('forum_replies')
            ->join('forums', 'forums.id', '=', 'forum_replies.forum_id')
            ->join('users', 'users.id', '=', 'forum_replies.user_id')
            ->select(
                'forum_replies.id as forum_replies_id',
                'forum_replies.description',
                'forum_replies.file',
                'forum_replies.created_at',
                'users.name as user_name',
                'users.email as user_email',
                'users.phone_number as user_phone_number',
            )
            ->where('forums.id', '=', $id)
            ->orderBy('forum_replies.id', 'desc')
            ->get();

            return response()->json([
                'success' => true,
                'forum' => $forum,
                'forum_replies' => $forumReplies
            ], Response::HTTP_OK);
        }

        return response()->json([
            'success' => false,
        ], 404);
    }

    public function storeReply(Request $request) {
        $user = JWTAuth::authenticate($request->token);
        if ($user) {

            if ($request->hasFile('file')) {
                $extension = $request->file('file')->getClientOriginalExtension();
                $file_name = 'REPLY_' . $request->title . '_' . time() . '.' . $extension;
    
                $request->file('file')->storeAs('public/forum', $file_name);
            } else {
                $file_name = NULL;
            }

            ForumReply::create([
                'forum_id' => $request->forum_id,
                'user_id' => $user->id,
                'description' => $request->description,
                'file' => $file_name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Forum Replied'
            ], Response::HTTP_OK);
        }

        return response()->json([
            'success' => false,
        ], 404);
    }

    public function getToken(Request $request) {
        $user = JWTAuth::authenticate($request->token);
        if ($user) {
        
            return response()->json([
                'success' => true,
            ], 200);
        }
        return response()->json([
            'success' => false,
        ], 404);
    }
}
