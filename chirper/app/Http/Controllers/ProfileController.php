<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Follow the user.
     *
     * @param $profileId
     *
     */
    public function followUser(int $profileId)
    {
    $user = User::find($profileId);
    if(! $user) {
        
        return redirect()->back()->with('error', 'User does not exist.'); 
    }

    $user->followers()->attach(auth()->user()->id);
    return redirect()->back()->with('success', 'Successfully followed the user.');
    }

    /**
     * Unfollow the user.
     *
     * @param $profileId
     *
     */
    public function unFollowUser(int $profileId)
    {
    $user = User::find($profileId);
    if(! $user) {
        
        return redirect()->back()->with('error', 'User does not exist.'); 
    }
    $user->followers()->detach(auth()->user()->id);
    return redirect()->back()->with('success', 'Successfully unfollowed the user.');
    }
}
