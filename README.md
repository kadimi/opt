## Press Apps Options Framework

### Purpose

The framework allows plugin authors to create advanced option pages in the matter of minutes.

### Installation

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

### Usage

#### Defining pages, tabs and options

Pages, tabs and options definitions for a plugin using PAF are found in the `paf/data` folder:

* `paf/data/pages.php` contains pages definitions
* `paf/data/tabs.php` contains tabs definitions
* `paf/data/options.php` contains options definitions

PAF comes with a few examples demonstrating the different features, you can use them as a starting point.

###Register Pages

Here is an example of defining a page:

```PHP
    <?php
    // wp-content/my_plugin/data/pages.php

    // Make sure our temporary variable is empty
    $pages = array();
    
    $pages[ 'page_a'] = array(
        'title'         => __( PAF 'Demo Page' ),   
        'menu_title'    => __( 'PAF Demo' ),     
    );
    
    // Register pages
    paf_pages( $pages );
```

###Register Tabs

Registering tabs work in the same way:

```PHP
    <?php
    // wp-content/my_plugin/data/tabs.php

    $tabs = array();
    
    $tabs[ 'tab_1'] = array(
        'title'         => __( 'Tab one' ),
        'menu_title'    => __( 'Tab 1' ),
        'page'          => __( 'page_a' ),
    );

    // Register tabs
    paf_tabs( $tabs );
```

----

###Register Options

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

#####Options Parameters

* `page` The PAF slug of the page the option belongs to.

* `tab` The PAF slug of the tab the option belongs to.

* `type (default=text)` The option type

  * `text`
  * `textarea`
  * `checkbox`
  * `radio`
  * `select`


* `title` The option title

* `subtitle` A small description under the option title

* `description` The text to show under the form field, setting it to `~` will instruct the framework to output the code that defines the current option. 

* `placeholder` The placeholder text

* `default` The default value, use arrays or comma separated values when working with `select`, `radio` or `checkbox`.

* `value` The value to show in the form for textual fields

* `selected` The value to show in the form for selection based fields, use arrays or comma separated values.

* `multiple` Tells `select` fields to allow multiple choice

* `options` Associative array of value/text pair that make the available choices for `select`, `radio` or `checkbox`. 

  **Tip:** Accepts also `posts` and `terms`
  
  **Tip:** If the text matches an image URL, the image is shown instead of the URL.
  
* `args` The parameter to pass to WordPress `get_posts()` or `get_terms()` when necessary, i.e, when the `options` parameter of a selection based field was set to `posts` or `terms`.

* `taxonomies (defaut=category,post_tag,link_category,post_format)` The taxonomies to query when using `terms` as a value for `options` on a selecteion based form field.

  
* `separator (default=<br />)`: The separator between `radio` and `checkbox` options

* `editor`: If set to true for a textarea, it will use a WYSIWYG editor.

* `editor_height`: An integer, the height in pixels of the WYSIWYG editor.

* `textarea_rows (default=20)`: An integer, the number of rows in the WYSIWYG editor.

* `teeny`: If set to true, WYSIWYG editor will have only a few icons.

* `media_buttons (default=TRUE)`: Weither to show the media upload button.
