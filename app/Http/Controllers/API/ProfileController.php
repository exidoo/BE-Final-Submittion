<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;

class ProfileController extends Controller
{
    public function store(ProfileRequest $request)
    {
        $currentUser = auth()->user();

        $profileData = Profile::updateOrCreate(
            ['user_id' => $currentUser->id],
            [
                'age' => $request['age'],
                'bio' => $request['bio'],
            ]
        );

        // Load the user relation
        $profileData->load('user');

        return response()->json([
            'message' => 'Profile berhasil diperbarui atau dibuat',
            'profile' => $profileData
        ], 200);
    }

    public function update(ProfileRequest $request, $id)
    {
        $profile = Profile::findOrFail($id);

        $profile->update([
            'age' => $request['age'],
            'bio' => $request['bio'],
        ]);

        // Load the user relation
        $profile->load('user');

        return response()->json([
            'message' => 'Profile berhasil diperbarui',
            'profile' => $profile
        ], 200);
    }
}
