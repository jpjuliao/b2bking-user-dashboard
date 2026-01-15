<?php
namespace JPJULIAO\B2BKing_Addons;

class Variations_columns
{
  public function __construct()
  {
    add_filter('b2bking_cream_hidecolumns_nameqty', [$this, 'show_subtotal']);
  }

  public function show_subtotal($hidecolumns)
  {
    $subtotal_key = array_search('subtotal', $hidecolumns);
    unset($hidecolumns[$subtotal_key]);
    return $hidecolumns;
  }
}