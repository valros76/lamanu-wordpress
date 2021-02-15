<?php

/**
 * @package Meteo
 * @version 1.0.0
 */
/*
Plugin Name: Meteo
Plugin URI: http://localhost/meteo/
Description: Just a tiny widget.
Author: Webdevoo
Version: 1.0.0
Author URI: https://webdevoo.com
*/

/**
 * defined('ABSPATH') bloque la visibilité du script php si quelqu'un va sur l'url
 */
defined('ABSPATH') or die('Vous n\'êtes pas autorisé à accéder à ce fichier !');
$meteo_debug = false;
// Enable shortcodes in widget text 
add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode', 11);

// Créer une méthode d'activation
// register_activation_hook(__FILE__, function () {
//    touch(__DIR__ . '/test');
// });

// Créer une méthode de désactivation
// register_deactivation_hook(__FILE__, function () {
//    unlink(__DIR__ . '/test');
// });

// Activer si besoin d'une fonction de désinstallation
// register_uninstall_hook(__FILE__, function(){
//    unlink(__DIR__);
// });
add_action('admin_enqueue_scripts', 'meteo_js');
// var_dump(meteo_js());
function meteo_js()
{
   wp_enqueue_script('meteo_script', plugins_url('meteo_script.js', __FILE__, array()));
   wp_localize_script('meteo_script', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('admin_menu', 'meteo_menu');
function meteo_menu()
{
   //add_menu_page($page_title,$menu_title,$capability, $menu_slug, $function, $icon_url, $position)
   add_menu_page(
      'meteo',
      'meteo',
      'manage_options',
      'meteo',
      'show_meteo',
      '../wp-content/themes/oceanwp-child/assets/img/parapluie.png',
      79
   );
}

function show_meteo()
{
   global $meteo_debug;
   $options_values = get_option('meteo_option_name'); ?>
   <h1>Bonjour,</h1>
   <form id="meteo-form" method="POST" action="options.php">
      <?php
      settings_fields('meteo_option_group');
      do_settings_sections('meteo');
      echo '
      <p>Pour débloquer le bouton d\'envoi si vous ajoutez un nom de ville (Française), cliquez hors du champs ID de la ville.</p>';
      $submit_meteo_attribute = array( 'id' => 'submit_meteo' );
      submit_button('Valider', 'primary', 'submit', true, $submit_meteo_attribute);
      ?>
   </form>
<?php

   if (!empty($options_values)) {
      /**
       * Utiliser le shortcode se fera avec do_shortcode('meteo_widget')
       */
      if (shortcode_exists('meteo_widget')) {
         echo '<p id="widget_preview">Copiez ce code dans un article pour afficher le widget : <b id="short-code">[' . do_shortcode('meteo_widget') . ']</b></p>';
         echo do_shortcode('[meteo_widget]');
      } else {
         echo '<p id="widget_preview">Copiez ce code dans un article pour afficher le widget : <b id="short-code"></b></p>';
      }
   };
}
function meteo_shortcode()
{
   $shortcode_options = get_option('meteo_option_name');
   if ($shortcode_options != false) {
      extract(shortcode_atts(array(
         'api_key' => $shortcode_options['api_key'] ?? 'e849a1bb2385f437c9ab3ce45ea1a5a1',
         'city_id' => is_numeric($shortcode_options['city_id']) ? $shortcode_options['city_id'] : '3038789',
         'widget_type' => 'openweathermap-widget-15'
      ), $shortcode_options));
      return '<div id="' . $widget_type . '"></div>
   <script async>
      window.myWidgetParam ? window.myWidgetParam : window.myWidgetParam = [];
      window.myWidgetParam.push({
         id: 15,
         cityid: "' . $city_id . '",
         appid: "' . $api_key . '",
         units: "metric",
         containerid: "' . $widget_type . '",
      });
      (function() {
         var script = document.createElement("script");
         script.async = true;
         script.src = "//openweathermap.org/themes/openweathermap/assets/vendor/owm/js/weather-widget-generator.js";
         var s = document.getElementsByTagName("script")[0];
         s.parentNode.insertBefore(script, s);
      })();
   </script>';
   }
}
// add_action('wp_ajax_update_meteo_options', 'update_meteo_options');
add_shortcode('meteo_widget', 'meteo_shortcode');


function api_key_callback()
{
   printf('<input type="text" name="meteo_option_name[api_key]" id="api_key" value="%s" readonly/>', 'e849a1bb2385f437c9ab3ce45ea1a5a1');
}
function city_code_callback()
{
   printf('<input type="text" name="meteo_option_name[city_id]" id="city_id" value="%s" placeholder="%s"/>', '3038789', '3038789');
}
function widget_type_callback()
{
   printf('<input type="text" name="meteo_option_name[widget_type]" id="widget_type" value="%s" readonly/>', 'openweathermap-widget-15');
}

function void_callback()
{
}

function meteo_fields()
{
   /**register_setting( 
    * string $option_group, 
    * string $option_name, 
    * array $args = array() 
    * ) 
    * */
   register_setting(
      'meteo_option_group',
      'meteo_option_name',
      'sanitizer'
   );

   add_settings_section(
      'meteo_setting_section',
      'Bienvenue sur votre plugin de météo',
      'void_callback',
      'meteo'
   );

   /**
    * add_settings_field(
    * string $id, 
    * string $title, 
    * callable $callback, 
    * string $page, 
    * string $section = 'default', 
    * array $args = array() 
    * )
    */
   add_settings_field(
      'api_key',
      'Entre votre clé API',
      'api_key_callback',
      'meteo',
      'meteo_setting_section'
   );

   add_settings_field(
      'city_code_id',
      'Entrez l\'ID de la ville',
      'city_code_callback',
      'meteo',
      'meteo_setting_section'
   );

   add_settings_field(
      'widget_type',
      'Choisissez votre widget',
      'widget_type_callback',
      'meteo',
      'meteo_setting_section'
   );
}
add_action('admin_init', 'meteo_fields');

function sanitizer($value)
{
   $values = array();
   if (isset($value['api_key']) && !empty($value['api_key'])) {
      $values['api_key'] = sanitize_text_field($value['api_key']);
   }
   if (isset($value['city_id']) && !empty($value['city_id'])) {
      $values['city_id'] = sanitize_text_field($value['city_id']);
   }
   if (isset($value['widget_type']) && !empty($value['widget_type'])) {
      $values['widget_type'] = sanitize_text_field($value['widget_type']);
   }
   return $values;
}



function meteo_css()
{
   echo '
   <style>
   #meteo-form{
      width:420px;
      display:grid;
      grid-template-columns:minmax(150px,1fr);
      grid-auto-rows:minmax(20px, auto);
      grid-auto-flow:row dense;
      gap:0.25em;
   }
   #meteo-form input[type="submit"]{
      max-width:250px;
      margin-left:auto;
      grid-column:1;
      grid-row:4;
   }
   #short-code{
      max-width:350px;
      max-height:350px;
      overflow-y:auto;
      display:block;
      background-color:#fff;
      padding:0.5em;
      word-wrap:break-word;
   }
   </style>
   ';
}

add_action('admin_head', 'meteo_css');
