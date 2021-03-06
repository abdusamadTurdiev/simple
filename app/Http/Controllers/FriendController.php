<?php

namespace App\Http\Controllers;

use Auth;
use App\User;

class FriendController extends Controller
{
    public function getIndex()
    {
    	$friends = Auth::user()->friends();
    	$requests = Auth::user()->friendRequests();

    	return view('friends.index')
    		->with('friends', $friends)
    		->with('requests', $requests);
    }

    public function getAdd($username)
    {
    	$user = User::where('username', $username)->first();

    	if (!$user) {
    		return redirect()->route('home');
    	}

    	if (Auth::user()->id === $user->id) {
    		return redirect()->route('home');
    	}

    	if (Auth::user()->hasFriendRequestPending($user) || $user->hasFriendRequestPending(Auth::user())) {
    		return redirect()
    			->route('profile.index', ['username' => $user->username]);
    	}

    	if (Auth::user()->isFriendsWith($user)) {
    		return redirect()
    			->route('profile.index', ['username' => $user->username]);
    	}

    	Auth::user()->addFriend($user);

    	return redirect()
    		->route('profile.index', ['username' => $user->username]);
    }

    public function getAccept($username)
    {
    	$user = User::where('username', $username)->first();

    	if (!$user) {
    		return redirect()->route('home');
    	}

    	if (!Auth::user()->hasFriendRequestRecieved($user)) {
    		return redirect()->route('home');
    	}

    	Auth::user()->acceptFriendRequest($user);

    	return redirect()
    		->route('profile.index', ['username' => $username]);
    }

    public function postDelete($username)
    {
        $user = User::where('username', $username)->first();

        if (!Auth::user()->isFriendsWith($user)) {
            return redirect()->back();
        }

        Auth::user()->deleteFriend($user);

        return redirect()->back();
    }
}
