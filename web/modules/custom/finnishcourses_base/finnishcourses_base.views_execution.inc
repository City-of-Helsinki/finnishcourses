<?php

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;


function finnishcourses_base_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
  if($view->id() == 'search_courses') {
    //kint($query->getWhere());
  }
}