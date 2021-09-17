<?php

namespace Drupal\finnishcourses_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\NodeInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'Go back link' block.
 *
 * @Block(
 *  id = "go_back_link",
 *  admin_label = @Translation("Go back link"),
 * )
 */
class GoBackLink extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function build() {

    $goBackLink = t('Go back');

    return [
      '#children' => '<a class="go-back-link" href="#" onclick="window.history.back()">'.$goBackLink.'</a>',
    ];
  }

}
