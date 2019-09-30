<?php

namespace Drupal\finnishcourses_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\NodeInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'Sitemap from menu links' block.
 *
 * @Block(
 *  id = "sitemap_menu_links",
 *  admin_label = @Translation("Sitemap from menu links"),
 * )
 */
class SitemapMenuLinks extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {

    $menuLinks = $this->getSitemapMenuLinks();


    return [
      '#theme' => 'health_research_form',
      '#form_action_url' => $form_action_url,
      '#signin_url' => $signin_url,
      '#forgot_pw_url' => $forgot_pw_url,
      //'#allowed_tags' => ['form', 'input'],
    ];
  }

  public function getSitemapMenuLinks() {
    $menus = array('main','footer', 'foo', 'foote');

    $combined_tree = array();
    $menu_tree = \Drupal::menuTree();
    $parameters = $menu_tree->getCurrentRouteMenuTreeParameters(trim($menus[0]));
    $manipulators = array(
      // Show links to nodes that are accessible for the current user.
      array('callable' => 'menu.default_tree_manipulators:checkNodeAccess'),
      // Only show links that are accessible for the current user.
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      // Use the default sorting of menu links.
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    // Force the entire tree to be build by setting expandParents to an
    // empty array.
    $parameters->expandedParents = array();
    // Iterate over the menus and merge them together.
    foreach($menus as $menu_name) {
      $tree_items = $menu_tree->load(trim($menu_name), $parameters);
      $tree_manipulated = $menu_tree->transform($tree_items, $manipulators);
      $combined_tree = array_merge($combined_tree, $tree_manipulated);
    }

    $menu = $menu_tree->build($combined_tree);

    return [
      '#markup' => \Drupal::service("renderer")->renderRoot($menu),
    ];
  }

}
