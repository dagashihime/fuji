<?php

namespace App\Services;

class ConnectionService
{
    /**
     * @param string $challengeMethod
     * 
     * @return array [codeChallenge, codeVerifier]
     */
    public static function generatePKCE(string $challengeMethod = "S256"): array
    {
        if(!in_array($challengeMethod, ['S256', 'plain'])) $challengeMethod = "S256"; // @ToDo: log error

        $codeVerifier = self::generateCodeVerifier();

        $codeChallenge = $challengeMethod === "plain"
            ? $codeVerifier
            : self::generateCodeChallenge($codeVerifier);
        
        return [$codeChallenge, $codeVerifier];
    }

    /**
     * @return string
     */
    private static function generateCodeVerifier(): string
    {
        $verifierBytes = random_bytes(64);
        $codeVerifier = session()->get('codeVerifier') 
            ?? rtrim(strtr(base64_encode($verifierBytes), "+/", "-_"), "=");
        session()->put('codeVerifier', $codeVerifier);

        return $codeVerifier;
    }

    /**
     * @param string $codeVerifier
     * 
     * @return string
     */
    private static function generateCodeChallenge(string $codeVerifier): string
    {
        dd($codeVerifier);
        $challengeBytes = hash("sha256", $codeVerifier, true);
        $codeChallenge = rtrim(strtr(base64_encode($challengeBytes), "+/", "-_"), "=");

        return $codeChallenge;
    }
}