<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User; //This is how follow/unfollow works
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
     */
    public function follow(Request $request, User $user): RedirectResponse
    {
        $request->user()->followings()->attach($user);

        return Redirect::back();
    }

    /**
     * Unfollow the user.
     */
    public function unfollow(Request $request, User $user): RedirectResponse
    {
        $request->user()->followings()->detach($user);

        return Redirect::back();
    }

    /**
     * Show the user details page.
     * @param int $userId
     *
     */
    public function show(int $userId)
    {
        $user = User::find($userId);
        $followers = $user->followers;
        $followings = $user->followings;
        return view('user.show', compact('user', 'followers' , 'followings'));
    }
    
    /**
     * Display a listing of the resource
     */
    public function followings(Request $request): View
    {
        return view('following.index', [
            'followings' => $request->user()->followings,
        ]);
    }
}
