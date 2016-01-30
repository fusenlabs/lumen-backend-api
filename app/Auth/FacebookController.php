<?php namespace App\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Auth;
use Socialite;

class FacebookController extends Controller {

    /**
    * Obtain the user information from Facebook.
    *
    * @return Response
    */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
        } catch (Exception $e) {
            return redirect('auth/facebook');
        }
 
        $authUser = $this->findOrCreateUser($user);
 
        Auth::login($authUser, true);
 
        return redirect()->route('home');
    }
 
    /**
    * Return user if exists; create and return if doesn't
    *
    * @param $facebookUser
    * @return User
    */
    private function findOrCreateUser($facebookUser)
    {
        $authUser = User::where('facebook_id', $facebookUser->id)->first();
 
        if ($authUser){
            return $authUser;
        }
 
        return User::create([
            'name' => $facebookUser->name,
            'email' => $facebookUser->email,
            'facebook_id' => $facebookUser->id,
            'avatar' => $facebookUser->avatar
        ]);
    }

    /**
    * 
    */
    public function verifyCredentials(Request $request) {
        // get posted credentials.
        // verify credentials against FB.
        // fetch user data.
        // check user existance: true => return user.
        // check user existance: false => register and return user.
        

        $request->input->get('oauth_token');
    }
}
