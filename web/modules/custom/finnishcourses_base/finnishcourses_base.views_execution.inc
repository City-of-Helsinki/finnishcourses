<?php

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;


function finnishcourses_base_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
  if($view->id() == 'search_courses') {
    //kint($query->getWhere());
  }
}

function finnishcourses_base_views_pre_build(ViewExecutable $view) {
  // if ($view->id() == 'content') {
  //   //$exposed_filters = ['langcode' => 'fi'];
  //   //$view->exposed_raw_input = array_merge($exposed_filters, (array)$view->exposed_raw_input);
  //   $view->filter['langcode']->value = ['fi' => 'fi'];
  //   //kint($view);
  // }
}