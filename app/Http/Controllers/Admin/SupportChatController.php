<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminMiddleware;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupportChatController extends Controller
{
    public function __construct()
    {
        $this->middleware(AdminMiddleware::class);
    }

    public function index()
    {
        $admin = Auth::user();

        $conversations = $this->buildConversations($admin);

        $activeUser = $conversations->first()['user'] ?? null;

        return view('admin.messages.index', [
            'conversations' => $conversations,
            'apiBase' => '/messages',
            'activeUserId' => $activeUser['id'] ?? null,
            'pageTitle' => 'Messages - Support',
        ]);
    }

    private function buildConversations($admin)
    {
        $contactIds = DB::table('messages')
            ->selectRaw('CASE WHEN sender_id = ? THEN user_id ELSE sender_id END as contact_id', [$admin->id])
            ->where(function ($query) use ($admin) {
                $query->where('sender_id', $admin->id)
                    ->orWhere('user_id', $admin->id);
            })
            ->whereRaw('CASE WHEN sender_id = ? THEN user_id ELSE sender_id END != ?', [$admin->id, $admin->id])
            ->distinct()
            ->pluck('contact_id');

        if ($contactIds->isEmpty()) {
            return collect();
        }

        $userIds = $contactIds->values();

        $lastMessages = Message::with('sender:id,name,email')
            ->whereIn('user_id', $userIds->merge([$admin->id]))
            ->whereIn('sender_id', $userIds->merge([$admin->id]))
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($m) use ($admin) {
                return $m->user_id === $admin->id ? $m->sender_id : $m->user_id;
            });

        $unreadCounts = Message::whereIn('user_id', $userIds)
            ->where('sender_id', $admin->id)
            ->where('is_read', false)
            ->select('user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')
            ->pluck('count', 'user_id');

        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        return $userIds->map(function ($userId) use ($users, $lastMessages, $unreadCounts, $admin) {
            $user = $users[$userId] ?? null;
            if (!$user) return null;

            $lastMsg = $lastMessages->get($userId)->first();

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'last_message' => $lastMsg?->message ?? '',
                'last_time' => $lastMsg?->created_at?->diffForHumans(),
                'last_created_at' => $lastMsg?->created_at?->toIso8601String(),
                'unread_count' => $unreadCounts->get($userId, 0),
            ];
        })->filter()->sortByDesc('last_created_at')->values();
    }

    public function listConversations()
    {
        $admin = Auth::user();

        return response()->json($this->buildConversations($admin));
    }

    public function show(User $user)
    {
        $conversations = $this->buildConversations(Auth::user());

        return view('admin.messages.index', [
            'conversations' => $conversations,
            'apiBase' => '/messages',
            'activeUserId' => $user->id,
            'pageTitle' => 'Messages - Support',
        ]);
    }

    public function list(Request $request, User $user): JsonResponse
    {
        $admin = Auth::user();

        $messages = Message::where(function ($query) use ($user, $admin) {
            $query->where('user_id', $admin->id)
                ->where('sender_id', $user->id);
        })
            ->orWhere(function ($query) use ($user, $admin) {
                $query->where('user_id', $user->id)
                    ->where('sender_id', $admin->id);
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'message' => $m->message,
                'sender_id' => $m->sender_id,
                'is_mine' => $m->sender_id === $admin->id,
                'time' => $m->created_at?->format('H:i'),
                'created_at' => $m->created_at?->toIso8601String(),
            ]);

        return response()->json($messages);
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $admin = Auth::user();

        $message = Message::create([
            'user_id' => $user->id,
            'sender_id' => $admin->id,
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
                    'time' => $message->created_at?->format('H:i'),
                    'created_at' => $message->created_at?->toIso8601String(),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Message envoyé.');
    }

    public function markRead(User $user): JsonResponse
    {
        $admin = Auth::user();

        Message::where('sender_id', $user->id)
            ->where('user_id', $admin->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function typing(Request $request, User $user): JsonResponse
    {
        $admin = Auth::user();
        $request->validate(['typing' => 'required|boolean']);

        cache()->put("admin_typing_{$admin->id}_{$user->id}", [
            'typing' => $request->boolean('typing'),
            'time' => now(),
        ], now()->addSeconds(10));

        return response()->json(['success' => true]);
    }

    public function checkTyping(User $user): JsonResponse
    {
        $data = cache()->get("portail_typing_{$user->id}");

        return response()->json([
            'typing' => $data && $data['typing'] && $data['time']->diffInSeconds(now()) < 8,
        ]);
    }

    public function sse(Request $request): StreamedResponse
    {
        $admin = Auth::user();
        $lastTime = $request->query('last_time', now()->subMinute()->toIso8601String());

        $response = new StreamedResponse(function () use ($admin, $lastTime) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no');

            $lastCheck = $lastTime;

            while (true) {
                if (connection_aborted()) break;

                $newMessages = Message::where(function ($q) use ($admin) {
                    $q->where('user_id', $admin->id)
                        ->orWhere('sender_id', $admin->id);
                })
                    ->where('created_at', '>', $lastCheck)
                    ->where('sender_id', '!=', $admin->id)
                    ->with('sender:id,name')
                    ->orderBy('created_at', 'asc')
                    ->get();

                if ($newMessages->isNotEmpty()) {
                    $data = $newMessages->map(fn($m) => [
                        'id' => $m->id,
                        'message' => $m->message,
                        'sender_id' => $m->sender_id,
                        'sender_name' => $m->sender?->name ?? 'Inconnu',
                        'is_mine' => false,
                        'time' => $m->created_at?->format('H:i'),
                        'created_at' => $m->created_at?->toIso8601String(),
                    ]);

                    echo "data: " . json_encode(['messages' => $data]) . "\n\n";
                    ob_flush();
                    flush();

                    $lastCheck = now()->toIso8601String();
                }

                // Check typing status for all active conversations
                $typingUsers = [];
                $activeUserIds = Message::where(function ($q) use ($admin) {
                    $q->where('user_id', $admin->id)->orWhere('sender_id', $admin->id);
                })
                    ->selectRaw('CASE WHEN sender_id = ? THEN user_id ELSE sender_id END AS contact_id', [$admin->id])
                    ->pluck('contact_id')
                    ->unique()
                    ->filter(fn($id) => $id !== $admin->id);

                foreach ($activeUserIds as $uid) {
                    $data = cache()->get("portail_typing_{$uid}");
                    if ($data && $data['typing'] && $data['time']->diffInSeconds(now()) < 8) {
                        $typingUsers[] = $uid;
                    }
                }

                if (!empty($typingUsers)) {
                    $names = User::whereIn('id', $typingUsers)->pluck('name', 'id');
                    $typingData = [];
                    foreach ($typingUsers as $uid) {
                        $typingData[] = ['id' => $uid, 'name' => $names[$uid] ?? 'Inconnu'];
                    }
                    echo "event: typing\ndata: " . json_encode($typingData) . "\n\n";
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
