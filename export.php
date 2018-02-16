<?php

date_default_timezone_set('America/Los_Angeles');

$config = json_decode(file_get_contents('./config.json'));

$threads_endpoint = 'https://disqus.com/api/3.0/threads/list?api_key=' . $config->api_key . '&forum=' . $config->forum . '&limit=' . $limit . '&order=' . $order . '&since=' . $since . "&cursor=" . $cursor;

function getPostsForThread($thread_id, $config) {
  $posts_endpoint = 'https://disqus.com/api/3.0/threads/listPosts.json?api_key=' . $config->api_key . '&forum=' . $config->forum . '&thread=' . $thread_id;

  $session = curl_init($posts_endpoint);
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($session);
  curl_close($session);

  return $result;
}
