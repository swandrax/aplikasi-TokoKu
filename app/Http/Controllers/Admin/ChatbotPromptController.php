<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotPrompt;
use Illuminate\Http\Request;

class ChatbotPromptController extends Controller
{
    public function index()
    {
        $prompts = ChatbotPrompt::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.chatbot_prompts.index', compact('prompts'));
    }

    public function create()
    {
        return view('admin.chatbot_prompts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'prompt_text' => 'required|string|max:1000',
        ]);

        ChatbotPrompt::create([
            'title' => $request->title,
            'prompt_text' => $request->prompt_text,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.chatbot-prompts.index')->with('success', 'Prompt berhasil ditambahkan.');
    }

    public function edit(ChatbotPrompt $chatbotPrompt)
    {
        return view('admin.chatbot_prompts.edit', compact('chatbotPrompt'));
    }

    public function update(Request $request, ChatbotPrompt $chatbotPrompt)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'prompt_text' => 'required|string|max:1000',
        ]);

        $chatbotPrompt->update([
            'title' => $request->title,
            'prompt_text' => $request->prompt_text,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.chatbot-prompts.index')->with('success', 'Prompt berhasil diperbarui.');
    }

    public function destroy(ChatbotPrompt $chatbotPrompt)
    {
        $chatbotPrompt->delete();
        return redirect()->route('admin.chatbot-prompts.index')->with('success', 'Prompt berhasil dihapus.');
    }
}
