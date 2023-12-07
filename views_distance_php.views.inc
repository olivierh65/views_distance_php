<?php

/**
 * @file
 * Contains views_distance_php\views_distance_php.views.inc..
 * Provide a custom views field data that isn't tied to any other module. */


/**
* Implements hook_views_data().
*/
function views_distance_php_views_data() {

    $data['views']['table']['group'] = t('Custom Global');
    $data['views']['table']['join'] = [
      // #global is a special flag which allows a table to appear all the time.
      '#global' => [],
    ];

    $data['views']['calc_distance_field'] = [
        'title' => t('Calcul distance'),
        'help' => t('Calcul la distance d\'une adresse'),
        'field' => [
            'id' => 'calc_distance_field',
        ],
    ];
    return $data;
}
