<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Routing\Controller;
use App\Actions\Fortify\UpdateAdminProfileInformation;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse;

class AdminProfileInformationController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return \Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse
     */
    public function update(Request $request,
    UpdateAdminProfileInformation $updater)
    {
        if (config('fortify.lowercase_usernames')) {
            $request->merge([
                Fortify::username() => Str::lower($request->{Fortify::username()}),
            ]);
        }


        $updater->update($request->user(), $request->all());

        return app(ProfileInformationUpdatedResponse::class);
    }
}
