<?php
  require __DIR__ . '/vendor/autoload.php';

  $options = array(
    'cluster' => 'us2',
    'useTLS' => true
  );
  $pusher = new Pusher\Pusher(
    'c28c5dc6a68db65e2590',
    '90c5eb55b40bc7f10daa',
    '2054403',
    $options
  );

  $data['message'] = 'hello world';
  $pusher->trigger('my-channel', 'my-event', $data);
?>