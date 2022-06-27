<?php


namespace eBot\Plugins\Helpers;


use eTools\Utils\Logger;

class DiscordTrait
{
    public static function sendMessageToWebhook($content,$url, DiscordWebhookEmbed $embeds=null){
        $username="eBot";

        $emb=null;

        if($embeds !== null){
            $emb=$embeds->getJson();
        }

        $postdata = json_encode(
            array(
                'username' => $username,
                'content' => $content,
                'embeds'=>$emb
            )
        );

        Logger::log($postdata);

        Logger::log(" - Perf $url");
		
		if(function_exists('curl_version')) {
			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postdata );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$result = curl_exec($ch);
			curl_close($ch);
		} else {
			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-Type: application/json',
					'content' => $postdata
				)
			);

			$context = stream_context_create($opts);
			Logger::log(" - Perf $url");
			$result = file_get_contents($url, false, $context);
		}
		

    }
}
