<?php
/**
 * @file
 * The primary PHP file for this theme.
 */

// Add scripts.min.js at end of body tag.
$theme_path = drupal_get_path('theme', 'sdi_bootstrap');
drupal_add_js(
  $theme_path . '/build/js-custom/sdi-scripts.min.js',
  [
    'type' => 'file',
    'scope' => 'footer',
  ]
);

/**
 * Implements hook_preprocess_html().
 */
function sdi_bootstrap_preprocess_html(&$variables) {
  // Add classes to body tag.
  $variables['classes_array'][] = drupal_html_class('page');
  $variables['classes_array'][] = drupal_html_class('page-parent');
  $variables['classes_array'][] = drupal_html_class('page-template-default');
  $variables['classes_array'][] = drupal_html_class('custom-background');
  $variables['classes_array'][] = drupal_html_class('searchPos1');
  $variables['classes_array'][] = drupal_html_class('hide-header-text');
  $variables['classes_array'][] = drupal_html_class('custom-font-enabled');
  $variables['classes_array'][] = drupal_html_class('single-author');
}

/**
 * Implements hook_form_alter().
 */
function sdi_bootstrap_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id === 'search_api_page_search_form_default_search') {
    // Modify placeholder text in search field.
    $form['keys_1']['#title'] = t('Search');
    $form['keys_1']['#attributes']['placeholder'] = t('Search...');
  }

}

/**
 * Implements hook_js_alter().
 */
function sdi_bootstrap_js_alter(&$javascript) {
  // Use updated jQuery library on all but some paths.
  $node_admin_paths = [
    'node/*/edit',
    'node/add',
    'node/add/*',
  ];
  $replace_jquery = TRUE;
  if (path_is_admin(current_path())) {
    $replace_jquery = FALSE;
  }
  else {
    foreach ($node_admin_paths as $node_admin_path) {
      if (drupal_match_path(current_path(), $node_admin_path)) {
        $replace_jquery = FALSE;
      }
    }
  }
  // Swap out jQuery to use an updated version of the library.
  if ($replace_jquery) {
    $javascript['misc/jquery.js']['data'] = drupal_get_path('theme', 'sdi_bootstrap') . '/js/jquery.min.js';
  }
}

/**
 * Implements hook_preprocess_entity().
 */
function sdi_bootstrap_preprocess_entity(&$variables, $hook) {
  // Enable use of function sdi_bootstrap_preprocess_entity_[entity_type]().
  $function = __FUNCTION__ . '_' . $variables['entity_type'];
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}

/**
 * Override or insert variables into the bean templates.
 */
function sdi_bootstrap_preprocess_entity_bean(&$vars) {
  // Replace webform container block with content from webform block. This is
  // required as webforms are displayed within bean block containers, in order
  // to allow placement using Context module.
  $bean = $vars['bean'];
  $webform_block_container_deltas = [
    'sdi_webform_feedback',
    'sdi_webform_comment_form',
  ];

  if (in_array($bean->delta, $webform_block_container_deltas)) {
    $webform_block_mapping = variable_get('webform_block_mapping', []);
    $webform_delta = 'client-block-' . $webform_block_mapping[$bean->delta];

    // Replace webform container block with title and content from webform
    // block.
    $block = module_invoke('webform', 'block_view', $webform_delta);
    $vars['title'] = $block['subject'];
    $vars['content'] = $block['content'];
  }
}

/**
 * Implements hook_breadcrumb().
 */
function sdi_bootstrap_breadcrumb($variables) {
  if(isset($variables['breadcrumb'])) {
    $breadcrumb = $variables['breadcrumb'];
    // Provide the opening ul.
    $output = t('<ul class="breadcrumb">');
    // Provide the menu crumbs.
    foreach($breadcrumb as $key => $value) {
      $output .= t('<li class="crumb crumb-' . $key . '">' . $value . '</li><li lass="crumb-divider">&gt;</li>');
    }

    // Provide the title crumb.
    $output .= t('<li class="crumb crumb-title"><a href="#">' . drupal_get_title() . '</a></li>');

    // Provide the closing ul.
    $output .= t('</ul>');
  }



  return $output;
}

/**
* hook_form_FORM_ID_alter
*/
function sdi_bootstrap_form_search_block_form_alter(&$form, &$form_state, $form_id) {
    $form['search_block_form']['#size'] = 25;  // define size of the textfield
    $form['#attributes']['onsubmit'] = "if(this.search_block_form.value=='Search'){ alert('Please enter a search'); return false; }";
    $form['search_block_form']['#attributes']['placeholder'] = '';
}

/**
 * Implements hook_bootstrap_search_form_wrapper().
 */
function sdi_bootstrap_bootstrap_search_form_wrapper($variables) {
  $output = '<div class="input-group input-group-sm">';
  $output .= $variables['element']['#children'];
  $output .= '<span class="input-group-btn">';
  $output .= '<button type="submit" class="btn btn-default search-submit">';
  // We can be sure that the font icons exist in CDN.
  if (theme_get_setting('bootstrap_cdn')) {
    $output .= _bootstrap_icon('search');
  }
  else {
    $output .= t('Search');
  }
  $output .= '</button>';
  $output .= '</span>';
  $output .= '</div>';
  return $output;
}
