<?php
/*
  Plugin Name: Cookie Consent
  Description: Simple WordPress plugin to show a cookie accept plugin.
  Author: Zenteo ApS
  Version: 1.0.1
  Author URI: https://www.zenteo.dk
 */

// Language
load_plugin_textdomain('cookie-consent', false, dirname(plugin_basename(__FILE__)) . '/languages');

//Add an option page for the settings
add_action('admin_menu', 'zenteo_cookie_plugin_menu');

function zenteo_cookie_plugin_menu()
{
    add_options_page(__('Cookie Consent', 'cookie-consent'), __('Cookie Consent', 'cookie-consent'), 'manage_options', 'zenteo_cookie_consent', 'CookieOptionsPage');
}

function CookieOptionsPage()
{
    ?>
    <div class="wrap">
        <?php screen_icon();
    ?>
        <h2><?php _e('Cookie Consent', 'cookie-consent');
    ?></h2>
        <div id="poststuff" class="metabox-holder has-right-sidebar">
            <div id="post-body">
                <div id="post-body-content">
                    <div class="meta-box-sortables">
                        <div class="postbox">
                            <h3 class="hndle"><?php _e('Your settings', 'cookie-consent');
    ?></h3>
                            <div class="inside">
                                <form action="options.php" method="post">				
                                    <?php settings_fields('zenteo_cookie_options');
    ?>
                                    <?php do_settings_sections('zenteo_cookie');
    ?>
                                    <input name="cat_submit" type="submit" id="submit" class="button-primary" style="margin-top:30px;" value="<?php esc_attr_e(__('Save Changes', 'cookie-consent'));
    ?>" />
                                    <?php
                                    $options = get_option('zenteo_cookie_options');
                                    $value = htmlentities($options['zenteo_cookie_link_settings'], ENT_QUOTES);
                                    if (!$value) {
                                        $value = __('cookie-policy', 'cookie-consent');
                                    }
                                    ?>
                                    <p><?php echo sprintf(__('Your Cookies Policy page is <a href="%s">here</a>. You may wish to create a menu item or other link on your site to this page.', 'cookie-consent'), home_url($value));
    ?></p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- poststuff -->
    </div>
    <?php

}
add_action('admin_init', 'zenteo_cookie_admin_init');

function zenteo_create_policy_page()
{
    //Check to see if the info page has been created
    $pagename = __('Cookie Policy', 'cookie-consent');
    $cpage = get_page_by_title($pagename);
    if (!$cpage) {
        global $user_ID;
        $page['post_type'] = 'page';
        $page['post_content'] = '<p>' . __('This site uses cookies - small text files that are placed on your machine to help the site provide a better user experience. In general, cookies are used to retain user preferences, store information for things like shopping carts, and provide anonymised tracking data to third party applications like Google Analytics. As a rule, cookies will make your browsing experience better. However, you may prefer to disable cookies on this site and on others. The most effective way to do this is to disable cookies in your browser. We suggest consulting the Help section of your browser or taking a look at <a href="http://www.aboutcookies.org">the About Cookies website</a> which offers guidance for all modern browsers', 'cookie-consent') . '</p>';
        $page['post_parent'] = 0;
        $page['post_author'] = $user_ID;
        $page['post_status'] = 'publish';
        $page['post_title'] = $pagename;
        wp_insert_post($page);
    }
}
register_activation_hook(__FILE__, 'zenteo_create_policy_page');

function zenteo_cookie_admin_init()
{
    register_setting('zenteo_cookie_options', 'zenteo_cookie_options', 'zenteo_cookie_options_validate');
    add_settings_section('zenteo_cookie_main', '', 'zenteo_cookie_section_text', 'zenteo_cookie', 'zenteo_cookie_main');
    add_settings_field('zenteo_cookie_text', __('Notification text', 'cookie-consent'), 'zenteo_cookie_text_settings', 'zenteo_cookie', 'zenteo_cookie_main');
    add_settings_field('zenteo_cookie_accept', __('Accept text', 'cookie-consent'), 'zenteo_cookie_accept_settings', 'zenteo_cookie', 'zenteo_cookie_main');
    add_settings_field('zenteo_cookie_more', __('More info text', 'cookie-consent'), 'zenteo_cookie_more_settings', 'zenteo_cookie', 'zenteo_cookie_main');
    add_settings_field('zenteo_cookie_link', __('Info page permalink', 'cookie-consent'), 'zenteo_cookie_link_settings', 'zenteo_cookie', 'zenteo_cookie_main');
    add_settings_field('zenteo_cookie_text_colour', __('Text colour', 'cookie-consent'), 'zenteo_cookie_text_colour_settings', 'zenteo_cookie', 'zenteo_cookie_main');
    add_settings_field('zenteo_cookie_link_colour', __('Link colour', 'cookie-consent'), 'zenteo_cookie_link_colour_settings', 'zenteo_cookie', 'zenteo_cookie_main');
    add_settings_field('zenteo_cookie_bg_colour', __('Bar colour', 'cookie-consent'), 'cookieBgColorSettings', 'zenteo_cookie', 'zenteo_cookie_main');
    add_settings_field('zenteo_cookie_button_colour', __('Button colour', 'cookie-consent'), 'CookieButtonColourSettings', 'zenteo_cookie', 'zenteo_cookie_main');
    add_settings_field('zenteo_cookie_bar_position', __('Notification position', 'cookie-consent'), 'zenteo_cookie_bar_position_settings', 'zenteo_cookie', 'zenteo_cookie_main');
}

function zenteo_cookie_section_text()
{
    echo '<p>' . __('You can just use these settings as they are or update the text as you wish. We recommend keeping it brief.', 'cookie-consent') . '<br />
		' . __('The plugin automatically creates a page called "Cookie Policy" and sets the default More Info link to yoursitename.com/cookie-policy.', 'cookie-consent') . '<br />
		' . __('If you find the page hasn\'t been created, hit the Save Changes button on this page.', 'cookie-consent') . '<br />
		' . __('If you would like to change the permalink, just update the Info page permalink setting, e.g. enter "?page_id=4" if you are using the default permalink settings (and 4 is the id of your new Cookie Policy page).', 'cookie-consent') . '</p>';
}

function zenteo_cookie_text_settings()
{
    $options = get_option('zenteo_cookie_options');
    $value = $options['zenteo_cookie_text_settings'];
    if (!$value) {
        $value = __('This website uses cookies to improve user experience. By using our website you consent to all cookies in accordance with our Cookie Policy.', 'cookie-consent');
    }
    echo '<input id="zenteo_cookie_text_settings" name="zenteo_cookie_options[zenteo_cookie_text_settings]" size="50" type="text" value="' . esc_attr($value) . '" />';
}

function zenteo_cookie_accept_settings()
{
    $options = get_option('zenteo_cookie_options');
    $value = $options['zenteo_cookie_accept_settings'];
    if (!$value) {
        $value = __('No problem', 'cookie-consent');
    }
    echo '<input id="zenteo_cookie_accept_settings" name="zenteo_cookie_options[zenteo_cookie_accept_settings]" size="50" type="text" value="' . esc_attr($value) . '" />';
}

function zenteo_cookie_more_settings()
{
    $options = get_option('zenteo_cookie_options');
    $value = $options['zenteo_cookie_more_settings'];
    if (!$value) {
        $value = __('More info', 'cookie-consent');
    }
    echo '<input id="zenteo_cookie_more_settings" name="zenteo_cookie_options[zenteo_cookie_more_settings]" size="50" type="text" value="' . esc_attr($value) . '" />';
}

function zenteo_cookie_link_settings()
{
    $options = get_option('zenteo_cookie_options');
    $value = $options['zenteo_cookie_link_settings'];
    if (!$value) {
        $value = __('cookie-policy', 'cookie-consent');
    }
    echo '<input id="zenteo_cookie_link_settings" name="zenteo_cookie_options[zenteo_cookie_link_settings]" size="50" type="text" value="' . esc_attr($value) . '" />';
}

function zenteo_cookie_text_colour_settings()
{
    $options = get_option('zenteo_cookie_options');
    $value = $options['zenteo_cookie_text_colour_settings'];
    if (!$value) {
        $value = '#dddddd';
    }

    ?>
    <input type="text" id="zenteo_cookie_text_colour" name="zenteo_cookie_options[zenteo_cookie_text_colour_settings]" value="<?php echo $value;
    ?>" class="my-color-field" />
    <?php

}

function zenteo_cookie_link_colour_settings()
{
    $options = get_option('zenteo_cookie_options');
    $value = $options['zenteo_cookie_link_colour_settings'];
    if (!$value) {
        $value = '#dddddd';
    }

    ?>
    <input type="text" name="zenteo_cookie_options[zenteo_cookie_link_colour_settings]" value="<?php echo $value;
    ?>" class="my-color-field" />
    <?php

}

function cookieBgColorSettings()
{
    $options = get_option('zenteo_cookie_options');
    $value = $options['cookieBgColorSettings'];
    if (!$value) {
        $value = '#464646';
    }

    ?>
    <input type="text" name="zenteo_cookie_options[cookieBgColorSettings]" value="<?php echo $value;
    ?>" class="my-color-field" />
    <?php

}

function CookieButtonColourSettings()
{
    $options = get_option('zenteo_cookie_options');
    $value = $options['CookieButtonColourSettings'];
    if (!$value) {
        $value = '#5cb85c';
    }

    ?>
    <input type="text" name="zenteo_cookie_options[CookieButtonColourSettings]" value="<?php echo $value;
    ?>" class="my-color-field" />
    <?php

}

function zenteo_cookie_bar_position_settings()
{
    $options = get_option('zenteo_cookie_options');
    $value = $options['zenteo_cookie_bar_position_settings'];

    ?>
    <select id="zenteo_cookie_bar_position_settings" name="zenteo_cookie_options[zenteo_cookie_bar_position_settings]" >';
        <option value="top" <?php if ($value == 'top') {
    ?> selected="selected" <?php 
}
    ?>>Top</option>;
        <option value="bottom" <?php if ($value == 'bottom') {
    ?> selected="selected" <?php 
}
    ?>>Bottom</option>;
    </select>
    <?php

}

function zenteo_cookie_options_validate($input)
{
    $options = get_option('zenteo_cookie_options');
    $options['zenteo_cookie_text_settings'] = trim($input['zenteo_cookie_text_settings']);
    $options['zenteo_cookie_accept_settings'] = trim($input['zenteo_cookie_accept_settings']);
    $options['zenteo_cookie_more_settings'] = trim($input['zenteo_cookie_more_settings']);
    $options['zenteo_cookie_link_settings'] = trim($input['zenteo_cookie_link_settings']);
    $options['zenteo_cookie_text_colour_settings'] = trim($input['zenteo_cookie_text_colour_settings']);
    $options['zenteo_cookie_link_colour_settings'] = trim($input['zenteo_cookie_link_colour_settings']);
    $options['cookieBgColorSettings'] = trim($input['cookieBgColorSettings']);
    $options['CookieButtonColourSettings'] = trim($input['CookieButtonColourSettings']);
    $options['zenteo_cookie_bar_position_settings'] = trim($input['zenteo_cookie_bar_position_settings']);
    return $options;
}

//Enqueue color-picker script for admin
function zenteo_color_picker()
{
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('cookie-consent-colour-picker', plugins_url('js/colour-picker.js', __FILE__), array('wp-color-picker'), false, true);
//	wp_enqueue_script( 'dashboard' );
}
add_action('admin_enqueue_scripts', 'zenteo_color_picker');

//Enqueue jquery
function zenteo_cookie_jquery()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('cookie-consent-js', plugins_url('js/cookie-consent-js.js', __FILE__), array('jquery'), '1.8', true);
    wp_enqueue_style('cookie-consent-css', plugins_url('/cookie.css', __FILE__), array(), '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'zenteo_cookie_jquery');

//Add CSS and JS
//Add some JS to the header to test whether the cookie option has been set
function addCookieCss()
{
    $options = get_option('zenteo_cookie_options');
    $text_colour = ($options['zenteo_cookie_text_colour_settings']) ? $options['zenteo_cookie_text_colour_settings'] : '#ddd';
    $link_colour = ($options['zenteo_cookie_link_colour_settings']) ? $options['zenteo_cookie_link_colour_settings'] : '#fff';
    $bg_colour = ($options['cookieBgColorSettings']) ? $options['cookieBgColorSettings'] : '#464646';
    $button_colour = ($options['CookieButtonColourSettings']) ? $options['CookieButtonColourSettings'] : '#5cb85c';
    $position = ($options['zenteo_cookie_bar_position_settings']) ? $options['zenteo_cookie_bar_position_settings'] : 'top'

    ?>
    <style type="text/css" media="screen">
        #zenteo-cookie-bar  { <?= $position ?>: 0; color: <?= $text_colour ?>; background-color: <?= $bg_colour ?>; }
        #zenteo-cookie-bar a,button#zenteoCookie  { color: <?= $link_colour;
    ?>; }
        button#zenteoCookie { background:<?= $button_colour;
    ?>; }
    </style>
    <?php

}
add_action('wp_head', 'addCookieCss');

//Add the notification bar
function addCookieBar()
{
    $options = get_option('zenteo_cookie_options');
    $current_text = ($options['zenteo_cookie_text_settings']) ? $options['zenteo_cookie_text_settings'] : __("This website uses cookies to improve user experience. By using our website you consent to all cookies in accordance with our Cookie Policy.", 'cookie-consent');
    $accept_text = ($options['zenteo_cookie_accept_settings']) ? $options['zenteo_cookie_accept_settings'] : __("I agree", 'cookie-consent');
    $more_text = ($options['zenteo_cookie_more_settings']) ? $options['zenteo_cookie_more_settings'] : __("Read more", 'cookie-consent');
    $link_text = ($options['zenteo_cookie_link_settings']) ? strtolower($options['zenteo_cookie_link_settings']) : strtolower(__("cookie-policy", 'cookie-consent'));

    $content = sprintf('<div id="zenteo-cookie-bar" class="hidden-print"><div class="inner">%s <a tabindex=1 href="%s">%s</a><button id="zenteoCookie" tabindex=1 onclick="zenteoAcceptCookies();">%s</button></div></div>', htmlspecialchars($current_text), home_url($link_text), htmlspecialchars($more_text), htmlspecialchars($accept_text));
    echo apply_filters('zenteo_cookie_content', $content, $options);
}
add_action('wp_footer', 'addCookieBar', 1000);
