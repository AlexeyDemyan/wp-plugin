<?php

/*
    Plugin name: Alex Test Plugin
    Description: Testing 1-2
    Version: 1.0
    Author: Alex
    Author URI: https://github.com/AlexeyDemyan
    Text Domain: wcpdomain
    Domain Path: /languages
*/

class WordCountAndTimePlugin
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'adminPage'));
        add_action('admin_init', array($this, 'settings'));
        add_filter('the_content', array($this, 'ifWrap'));
        add_action('init', array($this, 'languages'));
    }

    function languages()
    {
        load_plugin_textdomain('wcpdomain', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    function ifWrap($content)
    {
        if (is_main_query() and is_single() and (get_option('wcp_wordcount', '1') or get_option('wcp_charcount', '1') or get_option('wcp_readtime', '1'))) {
            return $this->createHTML($content);
        }
        return $content;
    }

    function createHTML($content)
    {
        $html = '<h3>' . esc_html(get_option('wcp_headline', 'Post Statistics')) . '</h3><p>';

        if (get_option('wcp_wordcount', '1') or get_option('wcp_readtime', '1')) {
            $wordCount = str_word_count(strip_tags($content));
        }

        if (get_option('wcp_wordcount', '1')) {
            $html .= esc_html__('This post has', 'wcpdomain') . ' ' . $wordCount . ' ' . esc_html__('words', 'wcpdomain') . '.<br>';
        }

        if (get_option('wcp_charcount', '1')) {
            $html .= esc_html__('This post has', 'wcpdomain') . ' ' . strlen(strip_tags($content)) . ' ' . esc_html__('characters', 'wcpdomain') . '.<br>';
        }

        if (get_option('wcp_readtime', '1')) {
            $html .= 'This post will take about ' . round($wordCount / 245) . ' minute(s) to read.<br>';
        }

        $html .= '</p>';

        if (get_option('wcp_location', '0') == '0') {
            return $html . $content;
        } else {
            return $content . $html;
        }
    }

    function settings()
    {
        // Registeting setting field in DB table:
        register_setting('wordcountplugin', 'wcp_location', array(
            // Here we're using the standard WP sanitize function called "sanitize_text_field"
            // Otherwise we would need to define and then reference our own sanitizer function
            'sanitize_callback' => array($this, 'sanitizeLocation'),
            'default' => '0'
        ));

        // Adding section for HTML elements in Settings:
        add_settings_section('wcp_first_section', null, null, 'word-count-settings-page');

        // Creating HTML element for setting:
        add_settings_field('wcp_location', 'Display Location', array($this, 'locationHTML'), 'word-count-settings-page', 'wcp_first_section');

        // Headline setting
        register_setting('wordcountplugin', 'wcp_headline', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Post Statistics'
        ));
        add_settings_field('wcp_headline', 'Headline Text', array($this, 'headlineHTML'), 'word-count-settings-page', 'wcp_first_section');

        // Word Count setting
        register_setting('wordcountplugin', 'wcp_wordcount', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
        add_settings_field('wcp_wordcount', 'Word Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_wordcount'));

        // Character Count setting
        register_setting('wordcountplugin', 'wcp_charcount', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
        add_settings_field('wcp_charcount', 'Character Count', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_charcount'));

        // Read Time setting
        register_setting('wordcountplugin', 'wcp_readtime', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '1'
        ));
        add_settings_field('wcp_readtime', 'Read Time', array($this, 'checkboxHTML'), 'word-count-settings-page', 'wcp_first_section', array('theName' => 'wcp_readtime'));
    }

    function sanitizeLocation($input)
    {
        if ($input != '1' and $input != '0') {
            add_settings_error('wcp_location', 'wcp_location_error', 'Display Location must be either Beginning or End');
            return get_option('wcp_location');
        }
        return $input;
    }

    function adminPage()
    {
        add_options_page('Word Count Settings', esc_html__('Word Count', 'wcpdomain'), 'manage_options', 'word-count-settings-page', array($this, 'myHTML'));
    }

    function locationHTML()
    { ?>
        <!-- Name has to match the setting -->
        <select name="wcp_location">
            <option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Beginning of Post</option>
            <option value="1" <?php selected(get_option('wcp_location'), '1') ?>>End of Post</option>
        </select>
    <?php }

    function checkboxHTML($args)
    { ?>
        <input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?>>
    <?php }

    function headlineHTML()
    { ?>
        <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
    <?php }

    function myHTML()
    { ?>
        <div class="wrap">
            <h1>Word Count Settings</h1>
            <!-- options.php is enough for WP to know what to do -->
            <form action="options.php" method="POST">
                <?php
                // WP will sort of hook up to the fields in DB:
                settings_fields('wordcountplugin');

                // And then WP will loop through registered settings sections and fields:
                do_settings_sections('word-count-settings-page');
                submit_button();
                ?>
            </form>
        </div>
<?php
    }
}

$wordCountAndTimePlugin = new WordCountAndTimePlugin();
