<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
</head>

<body>

<?php

require_once('vendor/j7mbo/twitter-api-php/TwitterAPIExchange.php');

require_once('TwitterTextFormatter.php');

// Use the class TwitterTextFormatter
use Netgloo\TwitterTextFormatter;

// Set here your application tokens
$settings = array(
  'oauth_access_token' => "YOUR_OAUTH_ACCESS_TOKEN",
  'oauth_access_token_secret' => "YOUR_OAUTH_ACCESS_TOKEN_SECRET",
  'consumer_key' => "YOUR_CONSUMER_KEY",
  'consumer_secret' => "YOUR_CONSUMER_SECRET"
);

// Set here the Twitter username where to getting latest tweets
$screen_name = 'netglooweb';

// Get timeline using TwitterAPIExchange
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$getfield = "?screen_name={$screen_name}";
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$user_timeline = $twitter
  ->setGetfield($getfield)
  ->buildOauth($url, $requestMethod)
  ->performRequest();
$user_timeline = json_decode($user_timeline);

// Print each tweet using TwitterTextFormatter to get the HTML text
echo "<ul>";
foreach ($user_timeline as $user_tweet) {
  echo "<li>";
  echo TwitterTextFormatter::format_text($user_tweet);
  echo "</li>";
}
echo "</ul>";

?>

</body>
</html>
