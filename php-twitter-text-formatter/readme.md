## Getting latest Tweets and displaying them in HTML with PHP

See here for more informations:
http://blog.netgloo.com/2015/08/16/php-getting-latest-tweets-and-displaying-them-in-html/

### Build and run

#### Prerequisites

- Composer > 1.0
- PHP > 5.5.9
- PHP cURL

#### Configurations

Open the file `example.php` and set your own tokens.

#### Build

Go in the project root's folder then run composer:

    $ composer update

### Usage

- Place the project folder under your Apache folder than navigate to the file
  example.php, for example: 
  `http://localhost:8888/php-twitter-text-formatter/example.php`.
- If you correctly configured the oAuth tokens will be displayed the latest
  tweets in HTML format.
