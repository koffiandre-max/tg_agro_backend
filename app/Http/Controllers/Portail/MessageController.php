<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $support = User::where('role', 'admin')->first();

        $messages = Message::where(function ($query) use ($user, $support) {
            $query->where('user_id', $user->id)
                ->where('sender_id', $support?->id ?? 0);
        })
            ->orWhere(function ($query) use ($user, $support) {
                $query->where('user_id', $support?->id ?? 0)
                    ->where('sender_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('portail.messages.index', compact('messages', 'support'));
    }

    public function list(Request $request): JsonResponse
    {
        $user = Auth::user();
        $support = User::where('role', 'admin')->first();

        $messages = Message::where(function ($query) use ($user, $support) {
            $query->where('user_id', $user->id)
                ->where('sender_id', $support?->id ?? 0);
        })
            ->orWhere(function ($query) use ($user, $support) {
                $query->where('user_id', $support?->id ?? 0)
                    ->where('sender_id', $user->id);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'message' => $m->message,
                'sender_id' => $m->sender_id,
                'is_mine' => $m->sender_id === $user->id,
                'time' => $m->created_at?->diffForHumans(),
                'created_at' => $m->created_at?->toIso8601String(),
            ]);

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();
        $support = User::where('role', 'admin')->first();

        if (!$support) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => 'Aucun support disponible.'], 503);
            }
            return redirect()->back()->with('error', 'Aucun support disponible.');
        }

        $message = Message::create([
            'user_id' => $support->id,
            'sender_id' => $user->id,
            'subject' => 'Support Chat',
            'message' => $request->input('message'),
            'is_read' => false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $message->sender_id,
                    'is_mine' => true,
                    'time' => $message->created_at?->diffForHumans(),
                    'created_at' => $message->created_at?->toIso8601String(),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Message envoyé avec succès.');
    }

    public function markRead(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        $message = Message::where('user_id', $user->id)->findOrFail($id);
        $message->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function typing(Request $request): JsonResponse
    {
        $request->validate(['typing' => 'required|boolean']);
        $user = Auth::user();
        $support = User::where('role', 'admin')->first();
        if (!$support) return response()->json(['success' => false], 400);

        cache()->put("portail_typing_{$user->id}", [
            'typing' => $request->boolean('typing'),
            'time' => now(),
        ], now()->addSeconds(10));

        return response()->json(['success' => true]);
    }

    public function sse(): StreamedResponse
    {
        $user = Auth::user();
        $support = User::where('role', 'admin')->first();
        $supportId = $support?->id ?? 0;

        $response = new StreamedResponse(function () use ($user, $supportId) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');

            $lastCheck = now()->subSeconds(5)->toIso8601String();

            while (true) {
                if (connection_aborted()) break;

                $newMessages = Message::where(function ($q) use ($user, $supportId) {
                    $q->where('user_id', $user->id)->where('sender_id', $supportId);
                })
                    ->where('created_at', '>', $lastCheck)
                    ->where('is_read', false)
                    ->orderBy('created_at', 'asc')
                    ->get();

                if ($newMessages->isNotEmpty()) {
                    $data = $newMessages->map(fn($m) => [
                        'id' => $m->id,
                        'message' => $m->message,
                        'sender_id' => $m->sender_id,
                        'sender_name' => 'Support TG\'AGRO',
                        'is_mine' => false,
                        'time' => $m->created_at?->format('H:i'),
                        'created_at' => $m->created_at?->toIso8601String(),
                    ]);

                    echo "data: " . json_encode(['messages' => $data]) . "\n\n";
                    ob_flush();
                    flush();
                    $lastCheck = now()->toIso8601String();
                }

                // Check typing from admin side
                $adminTyping = cache()->get("admin_typing_{$supportId}_{$user->id}");
                $isTyping = $adminTyping && $adminTyping['typing'] && $adminTyping['time']->diffInSeconds(now()) < 8;

                if ($isTyping) {
                    echo "event: typing\ndata: " . json_encode([['id' => $supportId, 'name' => 'Support']]) . "\n\n";
                    ob_flush();
                    flush();
                }

                sleep(2);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}
