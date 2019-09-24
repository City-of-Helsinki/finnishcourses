<?php

use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\query\QueryPluginBase;


function finnishcourses_base_views_query_alter(ViewExecutable $view, QueryPluginBase $query) {
  if($view->id() == 'search_courses') {
    // foreach top level condition group
    if (!empty($query->getWhere())) {
      // If extended starting level filter is selected the other starting level filter should not be used
      _alter_starting_levels($query->getWhere());
    }
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

function _alter_starting_levels(&$conditions) {
  $singleStartingLevel = FALSE;
  $unsettableLevelKey = FALSE;
  foreach ($conditions as $key => &$condition_group) {
    if (!empty($condition_group['conditions'])) {
      $correctField = FALSE;      
      
      foreach ($condition_group['conditions'] as $conditionValues) {
        if (is_array($conditionValues)) {
          foreach ($conditionValues as $conditionValue) {       
            if (!$correctField && $conditionValue == 'field_starting_level') {
              $correctField = TRUE;
            } else if ($correctField) {
              //kint($conditionValue);
              if (is_array($conditionValue)) {
                if (count($conditionValue) < 2) {
                  $singleStartingLevel = TRUE;
                } else if (count($conditionValue) > 1) {
                  $unsettableLevelKey = $key;
                }
              }
            }
          }
        }
      }
    }
  }
  if ($singleStartingLevel && $unsettableLevelKey) {
    unset($conditions[$unsettableLevelKey]);
  }
}