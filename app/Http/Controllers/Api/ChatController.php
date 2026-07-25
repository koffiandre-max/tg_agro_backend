<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private function supportUser()
    {
        return User::where('role', 'admin')->first();
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $support = $this->supportUser();

        $messages = Message::where(function ($query) use ($userId, $support) {
            $query->where('user_id', $userId)
                ->where('sender_id', $support->id ?? 0);
        })
            ->orWhere(function ($query) use ($userId, $support) {
                $query->where('user_id', $support->id ?? 0)
                    ->where('sender_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'message' => $m->message,
                'sender_id' => $m->sender_id,
                'is_mine' => $m->sender_id === $userId,
                'time' => $m->created_at?->diffForHumans(),
                'created_at' => $m->created_at?->toIso8601String(),
            ]);

        return response()->json($messages);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $support = $this->supportUser();

        if (!$support) {
            return response()->json(['success' => false, 'message' => 'Aucun support disponible.'], 404);
        }

        $message = Message::create([
            'user_id' => $support->id,
            'sender_id' => $user->id,
            'subject' => 'Support Chat',
            'message' => $request->input('message'),
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'is_mine' => true,
                'time' => 'À l\'instant',
                'created_at' => $message->created_at?->toIso8601String(),
            ],
        ]);
    }

    public function markRead(Request $request, $message): JsonResponse
    {
        $userId = Auth::id();
        $message = Message::where('user_id', $userId)->findOrFail($message);
        $message->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
