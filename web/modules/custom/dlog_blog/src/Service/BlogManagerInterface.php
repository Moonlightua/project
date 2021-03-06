<?php

namespace Drupal\dlog_blog\Service;

use Drupal\node\NodeInterface;

interface BlogManagerInterface{

  public function getRelatedPostsWithExactSameTags(NodeInterface $node, $limit = 2);

  public function getRelatedPostsWithSameTags(NodeInterface $node, array $exclude_ids = [], $limit = 2);

  public function getRandomPosts ($limit = 2, array $exclude_ids = []);

  public function getRelatedPosts(NodeInterface $node, $max = 4, $exact_tags = 2);

}
