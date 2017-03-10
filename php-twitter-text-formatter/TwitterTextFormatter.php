<?php

namespace Netgloo;

/**
 * Twitter Text Formatter : format twitter timeline text in html.
 * 
 * @author Netgloo <info@netgloo.com>
 */
class TwitterTextFormatter {

  // --------------------------------------------------------------------------

  /**
   * Return the tweet text formatted with the tweet's entities. 
   * 
   * Default configs:
   * 
   *   $configs = [
   *     'show_retweeted_by' => true,
   * 
   *     'retweeted_by_template' => 
   *       '<em> Retweeted by {{user_name}}</em>',
   * 
   *     'hashtag_link_template' => 
   *       '<a href="{{hashtag_link}}" rel="nofollow" target="_blank">' .
   *       '#{{hashtag_text}}</a>',
   * 
   *     'url_link_template' => 
   *       '<a href="{{url_link}}" rel="nofollow" target="_blank" ' .
   *       'title="{{url_title}}">{{url_text}}</a>',
   * 
   *     'user_mention_link_template' => 
   *       '<a href="{{user_mention_link}}" rel="nofollow" target="_blank" ' .
   *       title="{{user_mention_title}}">@{{user_mention_text}}</a>',
   * 
   *     'media_link_template' => 
   *       '<a href="{{media_link}}" rel="nofollow" target="_blank" ' .
   *       'title="{{media_title}}">{{media_text}}</a>'
   *   ];
   * 
   * @param $tweet (Object)
   * @param $configs (Array)
   * @return (String)
   */
  public static function format_text($tweet, $configs = []) {

    // Set up configs

    self::set_default(
      $configs,
      'show_retweeted_by',
      true
    );

    self::set_default(
      $configs,
      'retweeted_by_template',
      '<em> Retweeted by {{user_name}}</em>'
    );

    self::set_default(
      $configs,
      'hashtag_link_template',
      '<a href="{{hashtag_link}}" rel="nofollow" target="_blank">' . 
      '#{{hashtag_text}}</a>'
    );

    self::set_default(
      $configs,
      'url_link_template',
      '<a href="{{url_link}}" rel="nofollow" target="_blank" ' .
      'title="{{url_title}}">{{url_text}}</a>'
    );

    self::set_default(
      $configs,
      'user_mention_link_template',
      '<a href="{{user_mention_link}}" rel="nofollow" target="_blank" ' .
      'title="{{user_mention_title}}">@{{user_mention_text}}</a>'
    );

    self::set_default(
      $configs,
      'media_link_template',
      '<a href="{{media_link}}" rel="nofollow" target="_blank" ' .
      'title="{{media_title}}">{{media_text}}</a>'
    );

    // Is retweeted?
    if (isset($tweet->retweeted_status)) {

      $user_name = $tweet->user->name;
      $retweeted_by = '';

      // If show retweeted by, then prepare the "retweeted by" text
      if ($configs['show_retweeted_by']) {
        $retweeted_by = $configs['retweeted_by_template'];
        $retweeted_by = str_replace(
          '{{user_name}}', 
          $user_name, 
          $retweeted_by
        );
      }

      // Return the parsed re-tweet
      $res = self::parse_tweet_text($tweet->retweeted_status, $configs);
      return $res . $retweeted_by;
    }

    // Return the parsed tweet
    return self::parse_tweet_text($tweet, $configs);
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
  private static function parse_tweet_text($tweet, $configs) {

    // Collects the set of entities
    $entity_holder = array();

    // Hashtags
    if (isset($tweet->entities->hashtags)) {

      $template = $configs['hashtag_link_template'];

      foreach ($tweet->entities->hashtags as $hashtag) {

        // Link: https://twitter.com/hashtag/{{1}}?src=hash
        $hashtag_link = str_replace(
          '{{1}}', 
          strtolower($hashtag->text), 
          'https://twitter.com/hashtag/{{1}}?src=hash'
        );

        $replace = str_replace(
          '{{hashtag_link}}', 
          $hashtag_link, 
          $template
        );

        $replace = str_replace(
          '{{hashtag_text}}', 
          $hashtag->text, 
          $replace
        );

        self::add_entity($entity_holder, $hashtag, $replace);

      } // foreach

    } // if

    // Urls
    if (isset($tweet->entities->urls)) {

      $template = $configs['url_link_template'];

      foreach ($tweet->entities->urls as $url) {
        
        $replace = str_replace(
          '{{url_link}}',
          $url->url,
          $template
        );
        $replace = str_replace(
          '{{url_title}}',
          $url->expanded_url,
          $replace
        );
        $replace = str_replace(
          '{{url_text}}',
          $url->display_url,
          $replace
        );
        
        self::add_entity($entity_holder, $url, $replace);

      } // foreach

    } // if

    // User mentions
    if (isset($tweet->entities->user_mentions)) {

      $template = $configs['user_mention_link_template'];

      foreach ($tweet->entities->user_mentions as $user_mention) {

        // Link: https://twitter.com/{{1}}
        $user_mention_link = str_replace(
          '{{1}}', 
          strtolower($user_mention->screen_name), 
          'https://twitter.com/{{1}}'
        );

        $replace = str_replace(
          '{{user_mention_link}}', 
          $user_mention_link, 
          $template
        );
        $replace = str_replace(
          '{{user_mention_title}}', 
          $user_mention->name, 
          $replace
        );
        $replace = str_replace(
          '{{user_mention_text}}', 
          $user_mention->screen_name, 
          $replace
        );

        self::add_entity($entity_holder, $user_mention, $replace);

      } // foreach

    } // if

    // Media
    if (isset($tweet->entities->media)) {

      $template = $configs['media_link_template'];

      foreach ($tweet->entities->media as $media) {

        $replace = str_replace(
          '{{media_link}}',
          $media->url, 
          $template
        );
        $replace = str_replace(
          '{{media_title}}',
          $media->expanded_url, 
          $replace
        );
        $replace = str_replace(
          '{{media_text}}',
          $media->display_url, 
          $replace
        );

        self::add_entity($entity_holder, $media, $replace);

      } // foreach

    } // if

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
  private static function add_entity(
    &$entity_holder, 
    $tweet_entity, 
    $replace
  ) {
    
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
    $string, 
    $replacement, 
    $start, 
    $length = null, 
    $encoding = null
  ) {
    
    $strlen = mb_strlen($string, $encoding);
    $first_piece = mb_substr($string, 0, $start, $encoding) . $replacement;
    $second_piece = '';

    if (isset($length)) {
      $second_piece = mb_substr($string, $start + $length, $strlen, $encoding);
    }

    return $first_piece . $second_piece;
  }

  // --------------------------------------------------------------------------

  /**
   * Set a default value for the given key.
   */
  private static function set_default(&$array, $key, $default) {
    if (!isset($array[$key])) {
      $array[$key] = $default;
    }
    return;
  }

  // --------------------------------------------------------------------------

} // class
