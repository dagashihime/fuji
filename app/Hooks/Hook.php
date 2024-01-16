<?php

namespace App\Hooks;

class Hook
{
    /**
     * @param string $url
     * 
     * @return array
     */
    public static function call(string $url, ?bool $post = false, ?string $postFields = '', ?string $bearer = ''): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER => 1
        ]);

        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        }

        if($bearer) {
            curl_setopt_array($curl, [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json' , "Authorization: Bearer $bearer"],
            ]);
        }


        $response = curl_exec($curl);

        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header_text = substr($response, 0, $header_size);

        $header = [];
        foreach (array_filter(explode("\r\n", $header_text), fn ($l) => strlen($l) > 0) as $i => $line) {
            if ($i === 0) $header['http_code'] = $line;
            else {
                [$key, $value] = explode(': ', $line);
                $header[$key] = $value;
            }
        }

        $body = json_decode(substr($response, $header_size), true);

        return [$body, $header];
    }

    public static function callXML(string $url): array
    {
        $string = file_get_contents(
            filename: $url, 
            context: stream_context_create([
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false
                ]
            ])
        );
        $xml = simplexml_load_string(
            data: $string, 
            class_name: 'SimpleXMLElement', 
            options: LIBXML_NOCDATA
        );
        $namespaces = $xml->getNamespaces(true);

        $response = [];
        foreach ($xml as $x) {
            $response[] = $x->loc . "";
        }

        return [$response, $namespaces];
    }
}
