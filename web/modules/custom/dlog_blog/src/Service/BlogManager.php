<?php

namespace Drupal\dlog_blog\Service;


use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\node\NodeInterface;

class BlogManager implements BlogManagerInterface {

  /**
   * @var EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;
  protected EntityStorageInterface $nodeStorage;
  protected EntityViewBuilderInterface $nodeViewBuilder;

  public function __construct (EntityTypeManagerInterface $entity_type_manager){
      $this->entityTypeManager = $entity_type_manager;
      $this->nodeStorage = $entity_type_manager->getStorage('node');
      $this->nodeViewBuilder = $entity_type_manager->getViewBuilder('node');
  }

  public function getRelatedPostsWithExactSameTags(NodeInterface $node, $limit = 2){
    $result = &drupal_static(this::class . __METHOD__ . $node->id() . $limit);
    if (!isset($result)){
      if ($node->hasField('field_tags') && !$node->get('field_tags')->isEmpty()) {

        $query = $this->nodeStorage->getQuery()
          ->condition('status', NodeInterface::PUBLISHED)
          ->condition('type', 'article')
          ->condition('nid', $node->id(),'<>')
          ->range(0, $limit)
          ->addTag('entity_query_random');

        foreach($node->get('field_tags')->getValue() as $field_tag){
          $and = $query->andConditionGroup();
          $and->condition('field_tags',$field_tag['target_id']);
          $query->condition($and);
        }
        $result = $query->execute();
      }
      else{
        $result = [];
      }

    }
    return $result;
  }

  public function getRelatedPostsWithSameTags(NodeInterface $node, array $exclude_ids = [], $limit = 2){
    $result = &drupal_static(this::class . __METHOD__ . $node->id() . $limit);
    if (!isset($result)){
      if ($node->hasField('field_tags') && !$node->get('field_tags')->isEmpty()) {
        $field_tags_ids = [];
        foreach($node->get('field_tags')->getValue() as $field_tag){
          $field_tags_ids = $field_tag['target_id'];
        }

        $query = $this->nodeStorage->getQuery()
          ->condition('status', NodeInterface::PUBLISHED)
          ->condition('type', 'article')
          ->condition('nid', $node->id(),'<>')
          ->condition('field_tags', $field_tags_ids, 'IN')
          ->range(0,$limit)
          ->addTag('entity_query_random');

        if(!empty($exclude_ids)){
          $query->condition('nid', $exclude_ids,'NOT IN');
        }

        $result = $query->execute();
      }
      else{
        $result = [];
      }
    }

    return $result;
  }

  public function getRandomPosts ($limit = 2, array $exclude_ids = []){

    $query = $this->nodeStorage->getQuery()
      ->condition('status', NodeInterface::PUBLISHED)
      ->condition('type', 'article')
      ->range(0,$limit)
      ->addTag('entity_query_random');

    if(!empty($exclude_ids)){
      $query->condition('nid', $exclude_ids,'NOT IN');
    }

    return $query->execute();
  }

  public function getRelatedPosts(NodeInterface $node, $max = 4, $exact_tags = 2){
    $result = &drupal_static(this::class . __METHOD__ . $node->id() . $max . $exact_tags);
    if (!isset($result)){
      if($exact_tags > $max){
        $exact_tags = $max;
      }

      $counter = 0;
      $result = [];
      if ($exact_tags > 0){
        $exact_same = $this->getRelatedPostsWithExactSameTags($node, $exact_tags);
        $result += $exact_same;
        $counter += count($exact_same);
      }

      if ($counter < $max){
        $exclude_ids = [];
        if (!empty($exact_same)){
          $exclude_ids = $exact_same;
        }
        $same_tags = $this->getRelatedPostsWithSameTags($node, $exclude_ids, ($max - $counter));
        $result += $same_tags;
        $counter += count($same_tags);
      }

      if ($counter < $max){
        if (!empty($same_tags)){
          $exclude_ids += $same_tags;
        }

        $random = $this->getRandomPosts(($max - $counter), $exclude_ids);
        $result += $random;
      }
    }

    return $result;
  }
}

