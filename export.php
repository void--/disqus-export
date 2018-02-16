<?php

date_default_timezone_set('America/Los_Angeles');

$config = json_decode(file_get_contents('./config.json'));

$threads_endpoint = 'https://disqus.com/api/3.0/threads/list?api_key=' . $config->api_key . '&forum=' . $config->forum . '&limit=100&cursor=';

$data = [];

// Grab all the threads.
$data = getThreadsRecursive($threads_endpoint, $data, null);

// Grab post data for threads with posts.
foreach ($data as &$thread) {
  if ($thread->posts > 0) {
    $thread->post_data = getPostsForThread($thread->id, $config);
  }
}

// Filter out empty threads (if desired).
if ($config->exclude_empty_threads) {
  $data = array_filter($data, function($d) {
    return $d->posts > 0;
  });
}

// Export data to desired format.
switch ($config->export_format) {
  case 'csv_file':
    exportToCsv($data);
    break;
  case 'json_file':
    exportToJson($data);
    break;
  default:
    echo json_encode($data);
    break;
}

// Recursively get all threads for a specified forum.
function getThreadsRecursive($endpoint, $data, $cursor) {
  $result = curlGet($endpoint.$cursor);

  $threads = $result->response;
  $cursor  = $result->cursor;

  $data = array_merge($data, $threads);

  if ($cursor->more) {
    $data = getThreadsRecursive($endpoint, $data, $cursor->next);
  }

  return $data;
}

// Get all posts for a specified thread id.
function getPostsForThread($thread_id, $config) {
  $posts_endpoint = 'https://disqus.com/api/3.0/threads/listPosts.json?api_key=' . $config->api_key . '&forum=' . $config->forum . '&thread=' . $thread_id . '&limit=100';

  $result = curlGet($posts_endpoint);

  if ($result->cursor->more) {
    throw new Exception("More than 100 comments on thread {$thread_id}. This is currently not supported :/ pull requests welcome.");
  }

  return $result->response;
}

// Perform a 'get' operation with curl.
function curlGet($endpoint) {
  $session = curl_init($endpoint);
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
  $result = curl_exec($session);
  curl_close($session);

  return json_decode($result);
}

// Export to csv file.
function exportToCsv($data) {
  $fp = fopen('disqus_data.csv', 'w');
  fputcsv($fp, array_keys((array) $data['0']));
  foreach($data as $values){
      fputcsv($fp, (array) $values);
  }
  fclose($fp);
}

// Export to json file.
function exportToJson($data) {
  file_put_contents('disqus_data.json', json_encode($data));
}