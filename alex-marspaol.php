<?php

/*
    Plugin name: Alex Test Plugin
    Description: Testing 1-2
    Version: 1.0
    Author: Alex
    Author URI: https://github.com/AlexeyDemyan
*/

class WordCountAndTimePlugin
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'adminPage'));
    }

    function adminPage()
    {
        add_options_page('Word Count Settings', 'Word Count', 'manage_options', 'word-count-settings-page', array($this, 'myHTML'));
    }

    function myHTML()
    { ?>
        <div class="wrap">
            <h1>Word Count Settings</h1>
        </div>
<?php
    }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();
