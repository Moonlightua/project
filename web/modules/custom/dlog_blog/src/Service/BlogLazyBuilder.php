<?php

namespace Drupal\dlog_blog\Service;

class BlogLazyBuilder implements BlogLazyBuilderInterface {

  public static function randomBlogPosts() {

    return [
      '#theme' => 'dlog_blog_random_posts'
    ];

  }

}
