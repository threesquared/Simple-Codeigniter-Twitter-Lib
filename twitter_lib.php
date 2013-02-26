<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Twitter library
 * Used to get last 3 tweets from a user
 *
 * Ben Speakman <ben@d-formed.net>
 */
class Twitter_lib {

    public function __construct()
    {
        $this->CI = & get_instance();
        $this->CI->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));
        ini_set('precision', 20); //http://stackoverflow.com/a/8106127/908257
    }

    public function feed()
    {

        $user = 'd_formed'; // Twitter username here
        $url  = 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name='.$user.'&include_entities=1&count=3';

        if ( ! $content = $this->CI->cache->get($user.'.twitter') ) {

            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            $content = curl_exec($ch);
            curl_close($ch);

            // Cache for an hour
            $this->CI->cache->save($user.'.twitter', $content, 3600);

        }

        $content = json_decode($content);

        if (isset($content->error) || empty($content)) {
            return array();
        }

        return $content;      

    }

}