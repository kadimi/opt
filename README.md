## PressApps Options Framework

### Intro

The framework allows plugin authors to create advanced option pages very fast.

######Contents

* [Installation](#installation)
* [Usage](#usage)
* [File Structure](#file-structure)
* [Register Pages](#register-pages)
* [Register Tabs](#register-tabs)
* [Register Options](#register-options)

###<a name="installation"></a>Installation

Let's assume that you want to use PAF in your plugin called "My plugin" (and whose slug is most probably `my_plugin`)

* Drop the `paf` folder inside your plugin folder
* Include PAF's bootstrap file inside your plugin, for example:

```PHP
    <?php
    // wp-content/my_plugin/my_plugin.php

    /**
     * My plugin code
     */

    // Include PAF
    include dirname( __FILE__ ) . '/paf/main.php';
```

###<a name="usage"></a>Usage

You can access options you defined like this:

####All options values

```PHP
    <?php
    // wp-content/my_plugin/somewhere.php

    $all_my_options = paf();

    var_dump( $all_my_options );
```

####A single option value

```PHP
    <?php
    // wp-content/my_plugin/somewhere.php

    $my_option = paf( 'my_option_id' );

    var_dump( $my_option );
```

####A single option definition

This comes in handy when you want to know the default value of an option for example.

```PHP
    <?php
    // wp-content/my_plugin/somewhere.php

    $my_option = paf_d( 'my_option_id' );
    $my_option_default = isset( $my_option[ 'default' ] ) ? $my_option[ 'default' ] : FALSE;
    var_dump( $my_option_default );
```

###<a name="file-structure"></a>File Structure

> It's just pages, tabs and options

Pages, tabs and options definitions for a plugin using PAF are found in the `paf/data` folder:

* `paf/data/pages.php` contains pages definitions
* `paf/data/tabs.php` contains tabs definitions
* `paf/data/options.php` contains options definitions

PAF comes with a few examples demonstrating the different features, you can use them as a starting point.

###<a name="register-pages"></a>Register Pages

Here is an example of defining a page:

```PHP
    <?php
    // wp-content/my_plugin/data/pages.php

    // Make sure our temporary variable is empty
    $pages = array();
    
    $pages[ 'my_page_slug' ] = array(
        'title'         => __( 'PAF Demo Page' ),   
        'menu_title'    => __( 'PAF Demo' ),     
    );

    // Register pages
    paf_pages( $pages );
```

#####Pages Parameters

* `title` The page title

* `menu_title` The text for the page menu item

* `icon_url` The menu icon, ignored when using parent since subpages don't have icons in WordPress, this parameter accepts the same values you would use in WordPress own [add_menu_page()](http://codex.wordpress.org/Function_Reference/add_menu_page).

* `position` The position in the menu order this menu should appear, as you would use in [add_menu_page()](http://codex.wordpress.org/Function_Reference/add_menu_page).

* `parent` The slug name for the parent menu, as you would use in [add_submenu_page()](http://codex.wordpress.org/Function_Reference/add_submenu_page).

* `submit_button (default='Save Changes')` Text for the submit button.

* `reset_button` Text for the reset button, if ommitted, there will be no reset button.

* `success (default='Settings saved.')` Text for the success message.

###<a name="register-tabs"></a>Register Tabs

Registering tabs work in the same way:

```PHP
    <?php
    // wp-content/my_plugin/data/tabs.php

    $tabs = array();
    
    $tabs[ 'my_tab_slug'] = array(
        'title'         => __( 'Tab one' ),
        'menu_title'    => __( 'Tab 1' ),
        'page'          => __( 'my_page_slug' ),
    );

    // Register tabs
    paf_tabs( $tabs );
```

####Tabs Parameters

Most page parameters work for tabs as well but don't forget to specify which page the tabs belong to with the `page` parameter.

* `page` The PAF slug for the page the tab belongs to.

###<a name="register-options"></a>Register Options

Here is an example of defining a text field:

```PHP
    <?php
    // wp-content/my_plugin/data/options.php

    $options = array();
    
    $options[ 'my_option_name' ] = array(
        'type' => 'text',
        'page' => 'page_a',
        'title' => __( 'Welcome to my text field' ),
    );
```

####Options Parameters

* `page` The PAF slug of the page the option belongs to.

* `tab` The PAF slug of the tab the option belongs to.

* `type (default=text)` The option type

  * `text`
  * `textarea`
  * `checkbox`
  * `radio`
  * `select`
  * `media` produces an input field with upload functionality


* `title` The option title

* `subtitle` A small description under the option title

* `description` The text to show under the form field, setting it to `~` will instruct the framework to output the code that defines the current option. 

* `placeholder` The placeholder text

* `default` The default value, use arrays or comma separated values when working with `select`, `radio` or `checkbox`.

* `value` The value to show in the form for textual fields

* `colorpicker` If set to true for a text input field, it will become a color picker.

* `selected` The value to show in the form for selection based fields, use arrays or comma separated values.

* `multiple` Tells `select` fields to allow multiple choice

* `options` Associative array of value/text pair that make the available choices for `select`, `radio` or `checkbox`. 

  **Tip:** Accepts also `posts` and `terms`
  
  **Tip:** If the text matches an image URL, the image is shown instead of the URL.
  
* `args` The parameter to pass to WordPress `get_posts()` or `get_terms()` when necessary, i.e, when the `options` parameter of a selection based field was set to `posts` or `terms`.

* `taxonomies (defaut=category,post_tag,link_category,post_format)` The taxonomies to query when using `terms` as a value for `options` on a selection based form field.

  
* `separator (default=<br />)` The separator between `radio` and `checkbox` options

* `editor` If set to true for a textarea, it will use a WYSIWYG editor.

* `editor_height` An integer, the height in pixels of the WYSIWYG editor, see [this](http://wordpress.stackexchange.com/a/163260/17187) for more information about WYSIWYG height in WordPress.

* `textarea_rows (default=20)` An integer, the number of rows in the WYSIWYG editor, see [this](http://wordpress.stackexchange.com/a/163260/17187) for more information about WYSIWYG height in WordPress.

* `teeny` If set to true, the WYSIWYG editor will have less icons.

* `media_buttons (default=TRUE)` Weither to show the media upload button or not.
