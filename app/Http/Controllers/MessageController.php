<?php

namespace Modules\Portail\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Portail\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        return view('portail::messages.index');
    }

    public function list()
    {
        $messages = Message::where('user_id', Auth::id())
            ->with('sender:id,name')
            ->latest()
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'subject' => $m->subject,
                'message' => str($m->message)->limit(100),
                'is_read' => $m->is_read,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender->name,
                'created_at' => $m->created_at->toISOString(),
            ]);

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'parent_id' => 'nullable|exists:messages,id',
        ]);

        $data['user_id'] = Auth::id();
        $data['sender_id'] = Auth::id();

        // Si c'est une réponse, l'user_id reste celui du propriétaire original
        if ($data['parent_id'] ?? false) {
            $parent = Message::findOrFail($data['parent_id']);
            $data['user_id'] = $parent->user_id;
        }

        Message::create($data);

        return response()->json(['success' => true]);
    }

    public function markRead(Request $request)
    {
        $message = Message::where('user_id', Auth::id())->findOrFail($request->id);
        $message->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
