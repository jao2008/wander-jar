<?php

namespace App\Http\Controllers;

use App\Events\GroupMessageSent;
use App\Models\Group;
use App\Models\GroupMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupChatController extends Controller
{
    /**
     * Página do chat do grupo
     */
    public function show(Group $group): View
    {
        $membership = auth()->user()
            ->groups()
            ->whereKey($group->id)
            ->first();

        $role = $membership?->pivot?->role ?? 'member';

        $messages = GroupMessage::with('user:id,name')
            ->where('group_id', $group->id)
            ->orderBy('created_at')
            ->limit(200)
            ->get();

        return view('groups.chat', compact('group', 'messages', 'role'));
    }

    /**
     * Buscar mensagens do grupo (AJAX polling / refresh)
     */
    public function fetch(Request $request, Group $group): JsonResponse
    {
        $afterId = (int) $request->query('after', 0);

        $messagesQuery = GroupMessage::with('user:id,name')
            ->where('group_id', $group->id)
            ->orderBy('created_at');

        if ($afterId > 0) {
            $messagesQuery->where('id', '>', $afterId);
        } else {
            $messagesQuery->limit(200);
        }

        $messages = $messagesQuery->get()->map(function ($msg) use ($group) {
            return [
                'group_id'   => $group->id,
                'id'         => $msg->id,
                'user_id'    => $msg->user_id,
                'user_name'  => $msg->user->name,
                'body'       => $msg->body,
                'time'       => $msg->created_at->format('H:i'),
                'created_at' => $msg->created_at->toISOString(),
            ];
        });

        return response()->json([
            'ok' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Enviar mensagem (AJAX) + Broadcast realtime (Reverb)
     */
    public function store(Request $request, Group $group): JsonResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:800'],
        ]);

        $msg = GroupMessage::create([
            'group_id' => $group->id,
            'user_id'  => auth()->id(),
            'body'     => $data['body'],
        ]);

        $msg->load('user:id,name');

        $payload = [
            'group_id'   => $group->id,
            'id'         => $msg->id,
            'user_id'    => $msg->user_id,
            'user_name'  => $msg->user->name,
            'body'       => $msg->body,
            'time'       => $msg->created_at->format('H:i'),
            'created_at' => $msg->created_at->toISOString(),
        ];

        broadcast(new GroupMessageSent($payload))->toOthers();

        return response()->json([
            'ok' => true,
            'message' => $payload,
        ]);
    }
}