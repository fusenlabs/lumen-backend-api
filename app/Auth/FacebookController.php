<?php namespace App\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Facebook;
use Facebook\Authentication\AccessToken;

class FacebookController extends Controller {
    protected $currentUser = null;
    /**
    * Return user if exists; create and return if doesn't
    *
    * @param $facebookUser
    * @return User
    */
    private function findOrCreateUser(Facebook\GraphNodes\GraphUser $facebookUser)
    {
        $this->currentUser = $facebookUser['name'];
        // var_dump($facebookUser);exit;
        $authUser = User::where('facebook_id', $facebookUser['id'])->first();
        if ($authUser){
          $authUser->update(['avatar' => $facebookUser['picture']['url']]);
          return $authUser;
        }

        $hasher = app()->make('hash');
        return User::create([
            'name' => $facebookUser['name'],
            'email' => $facebookUser['email'],
            'password' => $hasher->make( app()->make('config')->get('app.pass_prefix') . $facebookUser['id'] ),
            'facebook_id' => $facebookUser['id'],
            'avatar' => $facebookUser['picture']['url']
        ]);
    }

    public function getLoggedUser() {
      return $this->currentUser;
    }


    /**
    * 
    */
    public function verifyCredentials(Request $request) {
        $config = app()->make('config');
        $fb = new Facebook\Facebook([
          'app_id' => $config->get('services.facebook.client_id'),
          'app_secret' => $config->get('services.facebook.client_secret'),
          'default_graph_version' => 'v2.5',
          //'default_access_token' => '{access-token}', // optional
        ]);

        $helper = $fb->getJavaScriptHelper();
        try {
          // $accessToken = $helper->getAccessToken();
          $accessToken = new AccessToken($request->input('accessToken'), $request->input('expiresIn'));
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          // When Graph returns an error
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          // When validation fails or other local issues
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }

        if (! isset($accessToken)) {
          echo 'No cookie set or no OAuth data could be obtained from cookie.';
          exit;
        }

        try {
          // Returns a `Facebook\FacebookResponse` object
          $response = $fb->get('/me?fields=id,name,email,picture,friends', $accessToken->getValue());
          /*$friendsResponse = $fb->get('/me/friends', $accessToken->getValue());
          //$result['data'];
          foreach($friendsResponse->getGraphEdge() as $node) {
            var_dump($node);
          }
          exit;*/
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }

        // Logged in

        $user = $response->getGraphUser();
        // var_dump($user);exit;
        // echo 'Name: ' . $user['name'];
        
        // $_SESSION['fb_access_token'] = (string) $accessToken;

        $authUser = $this->findOrCreateUser($user);

        app()['auth']->login($authUser, true);

        $hasher = app()->make('hash');
        /*var_dump( $hasher->make(
            "app()->make('config')->get('app.key')" . $authUser->facebook_id
        ));exit;*/
        return ['username' => $authUser->email, 'password' => app()->make('config')->get('app.pass_prefix') . $authUser->facebook_id];
 
        //return redirect()->route('home');
        // get posted credentials.
        // verify credentials against FB.
        // fetch user data.
        // check user existance: true => return user.
        // check user existance: false => register and return user.
    }
}
