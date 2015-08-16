<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
</head>

<body>

<?php

// Require J7mbo's TwitterAPIExchange library (used to retrive the tweets)
require_once('vendor/j7mbo/twitter-api-php/TwitterAPIExchange.php');

// Require our TwitterTextFormatter library
require_once('TwitterTextFormatter.php');

// Use the class TwitterTextFormatter
use Netgloo\TwitterTextFormatter;

// Set here your twitter application tokens
$settings = array(
  'oauth_access_token' => "2869623551-5naf4u3ooCed4aOy0OT6fuqv2lDp30aw92VxLFg",
  'oauth_access_token_secret' => "dCdVkjSVnLvuYUX1ddAkb3C7TK95Elq48U67prOpJ6U3a",
  'consumer_key' => "42nhJM7aIxxXUd9mRqDqHJKun",
  'consumer_secret' => "DhsjyOgJlFWA1c20JU5rkqUhJVY6hApx22yGPMUBvacbmMPqbU"
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

<p>
Visit our web site at <a href="http://netgloo.com">http://netgloo.com</a>
</p>

</body>
</html>
