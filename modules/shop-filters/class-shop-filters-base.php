<?php

namespace JPJULIAO\B2BKing_Addons;

abstract class Shop_Filters_Base
{
  protected function get_filter_values(string $key): array
  {
    if (!isset($_GET[$key])) {
      return [];
    }

    $value = $_GET[$key];

    if (is_array($value)) {
      return array_map('sanitize_text_field', $value);
    }

    if (is_string($value)) {
      $values = explode(',', $value);
      return array_map('sanitize_text_field', $values);
    }

    return [];
  }

  protected function render_simple_taxonomy_filter(string $taxonomy, string $title, string $param_name): string
  {
    $options = $this->get_terms_options($taxonomy);
    $current_filters = $this->get_filter_values($param_name);

    return $this->render_checkbox_list($title, $options, $param_name, $current_filters);
  }

  protected function get_terms_options(string $taxonomy): array
  {
    $terms = get_terms([
      'taxonomy' => $taxonomy,
      'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
      return [];
    }

    $options = [];
    foreach ($terms as $term) {
      $options[$term->slug] = $term->name;
    }

    return $options;
  }
}
