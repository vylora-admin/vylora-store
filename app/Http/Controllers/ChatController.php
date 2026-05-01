<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationChatChannel;
use App\Models\ApplicationChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Application $application)
    {
        $channels = $application->chatChannels()->withCount('messages')->latest()->get();

        return view('chat.index', compact('application', 'channels'));
    }

    public function store(Application $application, Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:64',
            'description' => 'nullable|string|max:500',
            'required_level' => 'nullable|integer|min:0|max:99',
        ]);
        ApplicationChatChannel::create([
            'application_id' => $application->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'required_level' => $validated['required_level'] ?? 0,
            'is_active' => true,
        ]);

        return back()->with('success', 'Channel created.');
    }

    public function show(Application $application, ApplicationChatChannel $channel)
    {
        abort_if($channel->application_id !== $application->id, 404);
        $messages = $channel->messages()->latest('id')->limit(200)->get()->reverse();

        return view('chat.show', compact('application', 'channel', 'messages'));
    }

    public function deleteMessage(Application $application, ApplicationChatChannel $channel, ApplicationChatMessage $message)
    {
        abort_if($channel->application_id !== $application->id, 404);
        abort_if($message->application_chat_channel_id !== $channel->id, 404);
        $message->delete();

        return back()->with('success', 'Message deleted.');
    }

    public function destroy(Application $application, ApplicationChatChannel $channel)
    {
        abort_if($channel->application_id !== $application->id, 404);
        $channel->delete();

        return back()->with('success', 'Channel deleted.');
    }
}
