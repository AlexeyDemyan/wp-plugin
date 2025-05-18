<?php

/*
    Plugin name: Alex Test Plugin
    Description: Testing 1-2
    Version: 1.0
    Author: Alex
    Author URI: https://github.com/AlexeyDemyan
*/

// Hooking up to a content of a post
add_filter('the_content', 'addToEndOfPost');

// Important to choose names that don't conflict with other plugins or WP Core
// Using Classes would fix this potential issue actually
function addToEndOfPost($content)
{
    if (is_single() and is_main_query()) {
        return $content . '<p>Hello from Alex</p>';
    }
    return $content;
};
