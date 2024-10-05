<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(SettingsUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return redirect(route('settings.edit'))->with('status', $request->has('user_agent') ? 'watcher-updated' :'pushover-updated');
    }
}
