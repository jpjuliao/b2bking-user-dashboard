<?php

namespace JPJULIAO\B2BKing_Addons;

class Shop_Filters
{

  public function __construct()
  {
    $this->init_base();
    $this->init_query();
    $this->init_renderer();
    $this->init_url_handler();
    $this->init_pjax();
  }

  private function init_base(): void
  {
    require_once __DIR__ . '/class-shop-filters-base.php';
  }

  private function init_query(): void
  {
    require_once __DIR__ . '/class-shop-filters-query.php';
    new Shop_Filters_Query();
  }

  private function init_renderer(): void
  {
    require_once __DIR__ . '/class-shop-filters-renderer.php';
    new Shop_Filters_Renderer();
  }

  private function init_url_handler(): void
  {
    require_once __DIR__ . '/class-shop-filters-url-handler.php';
    new Shop_Filters_URL_Handler();
  }

  private function init_pjax(): void
  {
    require_once __DIR__ . '/class-shop-filters-pjax.php';
    new Shop_Filters_Pjax();
  }
}
