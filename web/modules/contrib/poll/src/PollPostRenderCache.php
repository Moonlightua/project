<?php

namespace Drupal\poll;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Defines a service for poll post render cache callbacks.
 */
class PollPostRenderCache implements TrustedCallbackInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new PollPostRenderCache object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * @inheritDoc
   */
  public static function trustedCallbacks() {
    return ['renderViewForm'];
  }

  /**
   * Callback for #post_render_cache; replaces placeholder with poll view form.
   *
   * @param int $id
   *   The poll ID.
   * @param string $view_mode
   *   The view mode the poll should be rendered with.
   * @param string $langcode
   *   The langcode in which the poll should be rendered.
   *
   * @return array
   *   A renderable array containing the poll form.
   */
  public function renderViewForm($id, $view_mode, $langcode = NULL) {
    /** @var \Drupal\poll\PollInterface $poll */
    $poll = $this->entityTypeManager->getStorage('poll')->load($id);

    if ($poll) {
      if ($langcode && $poll->hasTranslation($langcode)) {
        $poll = $poll->getTranslation($langcode);
      }
      /** @var \Drupal\poll\Form\PollViewForm $form_object */
      $form_object = \Drupal::service('class_resolver')->getInstanceFromDefinition('Drupal\poll\Form\PollViewForm');
      $form_object->setPoll($poll);
      return \Drupal::formBuilder()->getForm($form_object, \Drupal::request(), $view_mode);
    }
    else {
      return ['#markup' => ''];
    }
  }

}
