<?php

/*
    Plugin name: Alex Test Plugin
    Description: Testing 1-2
    Version: 1.0
    Author: Alex
    Author URI: https://github.com/AlexeyDemyan
*/



// Function name still needs to be unique from WP Core or other plugins, unless we're using classes
add_action('admin_menu', 'myPluginSettingsLink');

function myPluginSettingsLink() {
    add_options_page('Word Count Settings', 'Word Count', 'manage_options', 'word-count-settings-page', 'mySettingsPageHTML');
}

function mySettingsPageHTML () { ?>
    Hello from new plugin
<?php 
}
