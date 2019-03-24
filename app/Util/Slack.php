<?php

namespace App\Util;

class Slack
{
    /**
     * $message = array(
     *     'username' => 'Bot',
     *     'text' => 'fooooo!!!',
     * );
     * send($message);
     */
    public function send($message) {
      $webhook_url = getenv('SLACK_INCOMING_HEBHOOKS');

      $options = array(
        'http' => array(
          'method' => 'POST',
          'header' => 'Content-Type: application/json',
          'content' => json_encode($message),
        )
      );
      $response = file_get_contents($webhook_url, false, stream_context_create($options));
      return $response === 'ok';
    }
}
