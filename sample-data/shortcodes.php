<?php
/**
 * Shortcodes definitions
 * 
 * @package skelet
 */

// Make sure our temporary variable is empty
$shortcodes = array();

/**
 * $dir in this example translates to where this file resides relative to the site
 * domain name, ex: "/wp/wp-content/plugins/some_plugin/skelet". Use it to set the
 * icon paths, so if your icon is in "/wp/wp-content/plugins/some_plugin/skelet/dummy.png"
 * set "image" to "$dir/dummy.png".
 */
$dir = parse_url( site_url(), PHP_URL_PATH ) . str_replace( ABSPATH, '/', dirname( __FILE__ ) );

$shortcodes[ 'my_demo_shortcode' ] = array(
	'title'     => __( 'My demo shortcode' ),
	'function'  => 'raw',
	'image'     => "$dir/shortcode.png",
);

$shortcodes[ 'my_demo_shortcode_2' ] = array(
	'title'     => __( 'My demo shortcode 2' ),
	'function'  => 'raw',
	'image'     => "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gkaExIR2nBPfQAAA1FJREFUOMttk19IW3ccxc/v3mtuc2PU6OqmZvFPMYl/cg0Bt77FNzF7KAXX0nYM19KH0b0U2ifpUx/20Ie+FTboH1oG0uVlgbk8KO3VMSndameI+ecSo0IwNsFgEnNzc+93D6vixs7z+R4Oh++H4YQ8Hg8ikQi6urqmv7p61e8cGXEBQHJ9PfH44UMll8sFZVnG2tra8Q07GeBwOPyzd+/O+ySzVP0lTGo0yhiAU8PDJE1Ost/r9eq3d+4ENrNZBf/V1OTkjV8VhYIul/HKZqOt0VHa9nop29tL2+Pj9Kq5mUJOp/Hbixf02dTUjeMGsiyjVCr5nzx69LJ47hyGz5xBTtfxDkDdZAIzmyHwPGz5PPpEEevJJDpCIXx5/fqEtaXlnyb3HzyoPPd4jCe9vfR8bo4SOzu0Xy7ToapSTVXpsFaj7UKBfrh5k1asVvrJ7Tbuf/9dBQA4u90+PWKxSM3JJLMKAqbOn0d3aysEAIZhABwHvdFAm8mEz+/dQ3FiAuLWFhvnBelju32av3Lli68HU6nxU+k0Y6oKunQJ7VYrYBhIPHuG7dlZ7APolGXomoaDbBam1VXUBYEaY2N57kN7j6uaSrEmjoNdFBHr70dJ0wDGoMXjUF+/xl/lMhhjaDKbcfjmDTgA1Y0N1tnT4xK0eh30ftGmRgOdZjMaug7wPD66dQuN27dx1mYDJwj4c24OpxcXURZFEGPQ6nVwO5ubCc7hIDIMWHQdLboO3TAAAKmFBXR3d2MjEkFsZgb9166hVRRhEIF3OGgnk0lwS8qSsu/1MrFSQTMRxBMBH0Sj2GIMbr8f7nAYZkmCRASuUkHZ52PLy0sKl9nMBP/I5aqHbjcxXYdgGGCSBJPFAs5kQn97OxqSBCICYwyCYUAYHKSXe3vVTDoT5GRZxsL8fGD5wgVW43l0trRg3+dDfHQUlqdPob/fhwAQERoAlMuX2WIoFPDIHvC7u7soFApZTVXf7V28GHDkcjSUTrM2VYVV045fna9Wkevro8czM+zncPib1bdvf8zv5v8Nk8vt8ns/PTv/SVubNBaL0elikTEAxY4Oig4NMaVUqkZWVgKxeFz5XxqPcHY6ndMDAwN+0Wp1AUDt4CCRSaeVZDIZPPIc6W+3V4XxNBWrqAAAAABJRU5ErkJggg==",
);

$shortcodes[ 'my_shortcode_keyboard' ] = array(
	'title'    => __( 'Keyboard' ),
	'function' => 'raw',
	'image'    => 'http://findicons.com/files/icons/85/kids/32/keyboard.png',
);

$shortcodes[ 'my_shortcode_text' ] = array(
	'title'      => __( 'Text' ),
	'function'   => 'raw',
	'text'       => 'Text title',
);

// Register options
paf_shortcodes( $shortcodes );
