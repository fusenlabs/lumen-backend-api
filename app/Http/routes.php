<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->post('login', function() use($app) {
    $credentials = app()->make('request')->input("credentials");
    $response = $app->make('App\Auth\Proxy')->attemptLogin($credentials);
    return $response;
});

$app->post('login/facebook', function() use($app) {
    $fb = $app->make('App\Auth\FacebookController');
    $credentials = $fb->verifyCredentials(app()->make('request'));
    //return response()->json($app->make('oauth2-server.authorizer')->issueAccessToken());
    $response = $app->make('App\Auth\Proxy')->attemptLogin($credentials);
    $data = json_decode($response->content());
    // $data->facebookUser = $fb->getLoggedUser();
    return response()->json($data);
});

$app->post('refresh-token', function() use($app) {
    return $app->make('App\Auth\Proxy')->attemptRefresh();
});

$app->post('oauth/access-token', function() use($app) {
    /*$requestDomain = isset($_SERVER['HTTP_HOST'])
        ? $_SERVER['HTTP_HOST']
        : $_SERVER['SERVER_NAME'];

    $validUrls = $app->make('db')
        ->table('oauth_client_reference_http')
        ->select('accept_from_url')
        ->where('client_id', '=', $app->make('request')->get('client_id'))
        ->get();

    $checkUrls = count($validUrls) !== 0;
    if ($checkUrls) {
        $hasMatch = array_reduce($validUrls, function($carry, $item) use ($requestDomain)
        {
            $pattern = '/'.str_replace('*.', '^([a-z0-9]+[.])*', $item->accept_from_url).'$/';
            $match = preg_grep($pattern, [$requestDomain]);
            return $carry || $match;
        });

        if (!$hasMatch) {
            throw new App\Exceptions\InvalidClientReferenceException();
        }
    }*/
    return response()->json($app->make('oauth2-server.authorizer')->issueAccessToken());
});

$app->group(['prefix' => 'api', 'middleware' => 'oauth'], function($app)
{
    $app->get('resource', function() {
        $authManager = app()['oauth2-server.authorizer'];
        $userId = $authManager->getResourceOwnerId();
        //build user relative to resource owner id
        $user = App\Auth\User::where('id', $userId)->first();

        return response()->json([
            "id" => $user->id,
            "name" => $user->name
        ]);
    });
});


$app->group(['prefix' => 'user', 'middleware' => 'oauth'], function () use ($app)
{


    // user/{id}/password_reset
    // user/{id}/password_change
    // user/{id}/deactivate
    // user/{id}/profile
});
// me/
