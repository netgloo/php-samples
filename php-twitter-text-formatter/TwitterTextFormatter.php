<?php

namespace Netgloo;

/**
 * Twitter Text Formatter : format twitter timeline text in html.
 * 
 * @author   Netgloo <info@netgloo.com>
 * @version  1.0
 */
class TwitterTextFormatter {

  // --------------------------------------------------------------------------

  /**
   * Return the tweet text formatted with the tweet's entities. 
   * 
   * @param $tweet (Object)
   * @return (String)
   */
  public static function format_text($tweet, $show_retweeted_by = true) {

    // Retweeted
    if (isset($tweet->retweeted_status)) {
      $user_name = $tweet->user->name;
      $retweeted_by = $show_retweeted_by ?
        "<em> Retweeted by {$user_name}</em>" : '';
      return self::parse_tweet_text($tweet->retweeted_status) . $retweeted_by;
    }

    return self::parse_tweet_text($tweet);
  }

  // --------------------------------------------------------------------------

  // ==========================================================================
  // PRIVATE METHODS

  // --------------------------------------------------------------------------

  /**
   * Return the formatted text taking entities from the $tweet object.
   * 
   * Credits: this function is a modified version of the one from Jacob 
   * Emerick's Blog (http://goo.gl/lhu8Ix)
   */
  private static function parse_tweet_text($tweet) {

    // Define patterns for each entity
    $hashtag_link_pattern = 
      '<a href="http://twitter.com/search?q=%23{{1}}&src=hash" ' . 
      'rel="nofollow" target="_blank">#{{2}}</a>';
    $url_link_pattern = 
      '<a href="{{1}}" rel="nofollow" target="_blank" title="{{2}}">{{3}}</a>';
    $user_mention_link_pattern = 
      '<a href="http://twitter.com/{{1}}" rel="nofollow" target="_blank" ' . 
      'title="{{2}}">@{{3}}</a>';
    $media_link_pattern = 
      '<a href="{{1}}" rel="nofollow" target="_blank" title="{{2}}">{{3}}</a>';

    // Collects the set of entities
    $entity_holder = array();

    if (isset($tweet->entities->hashtags)) {
      foreach ($tweet->entities->hashtags as $hashtag) {
        $replace = $hashtag_link_pattern;
        $replace = str_replace('{{1}}', strtolower($hashtag->text), $replace);
        $replace = str_replace('{{2}}', $hashtag->text, $replace);
        self::add_entity($entity_holder, $hashtag, $replace);
      }
    }

    if (isset($tweet->entities->urls)) {
      foreach ($tweet->entities->urls as $url) {
        $replace = $url_link_pattern;
        $replace = str_replace('{{1}}', $url->url, $replace);
        $replace = str_replace('{{2}}', $url->expanded_url, $replace);
        $replace = str_replace('{{3}}', $url->display_url, $replace);
        self::add_entity($entity_holder, $url, $replace);
      }  
    }

    if (isset($tweet->entities->user_mentions)) {
      foreach ($tweet->entities->user_mentions as $user_mention) {
        $replace = $user_mention_link_pattern;
        $replace = str_replace(
          '{{1}}', strtolower($user_mention->screen_name), $replace);
        $replace = str_replace('{{2}}', $user_mention->name, $replace);
        $replace = str_replace('{{3}}', $user_mention->screen_name, $replace);
        self::add_entity($entity_holder, $user_mention, $replace);
      }
    }

    if (isset($tweet->entities->media)) {
      foreach ($tweet->entities->media as $media) {
        $replace = $media_link_pattern;
        $replace = str_replace('{{1}}', $media->url, $replace);
        $replace = str_replace('{{2}}', $media->expanded_url, $replace);
        $replace = str_replace('{{3}}', $media->display_url, $replace);
        self::add_entity($entity_holder, $media, $replace);
      }
    }

    // Sort the entities in reverse order by their starting index
    krsort($entity_holder);

    // Replace the tweet's text with the entities
    $text = $tweet->text;
    foreach ($entity_holder as $entity) {
      $text = self::mb_substr_replace(
        $text, 
        $entity->replace, 
        $entity->start, 
        $entity->length, 
        'utf-8'
      );
    }

    return $text;
  }

  // --------------------------------------------------------------------------

  /**
   * Add an entity to the entity_holder.
   */
  private static function add_entity(&$entity_holder, $tweet_entity, $replace) {
    
    $entity = new \stdClass();
    $entity->start = $tweet_entity->indices[0];
    $entity->end = $tweet_entity->indices[1];
    $entity->length = $entity->end - $entity->start;
    $entity->replace = $replace;
    $entity_holder[$entity->start] = $entity;

    return;
  }

  // --------------------------------------------------------------------------

  /**
   * String replacement supporting UTF-8 encoding.
   */
  private static function mb_substr_replace(
      $string, $replacement, $start, $length = null, $encoding = null) {
    
    $strlen = mb_strlen($string, $encoding);
    $first_piece = mb_substr($string, 0, $start, $encoding) . $replacement;
    $second_piece = '';

    if (isset($length)) {
      $second_piece = mb_substr($string, $start + $length, $strlen, $encoding);
    }

    return $first_piece . $second_piece;
  }

  // --------------------------------------------------------------------------

} // class TwitterTextFormatter
