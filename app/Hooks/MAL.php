<?php

namespace App\Hooks;

class MAL extends Hook
{
    /**
     * @param string $code
     * 
     * @return array
     */
    public static function authorize(string $code): array
    {
        $params = collect([
            "client_id" => env('MAL_CLIENT_ID'),
            "client_secret" => env('MAL_CLIENT_SECRET'),
            "grant_type" => 'authorization_code',
            "code" => $code,
            "redirect_uri" => route('api.mal.auth'),
            "code_verifier" => session()->get('codeVerifier')
        ])->map(fn ($value, $key) => "$key=$value")->implode('&');

        [$body, $header] = self::call("https://myanimelist.net/v1/oauth2/token", post: true, postFields: $params);

        session()->forget('codeVerifier');

        return $body;
    }

    /**
     * @return array [body, header]
     */
    public static function getUserList(): array
    {
        [$accessToken] = auth()->user()
            ->pkce_connections
            ->where('domain', 'https://api.myanimelist.net')
            ->pluck('access_token');

        $params = collect([
            "fields" => "num_episodes,genres,start_date,end_date,list_status{num_times_rewatched}",
            "limit" => 1000
        ])->map(fn ($value, $key) => "$key=$value")->implode('&');

        // @ToDo: loop if result is 1000
        $response = self::call("https://api.myanimelist.net/v2/users/@me/animelist?$params", bearer: $accessToken);

        return $response;
    }
}
