<?php

namespace Drupal\views_distance_php\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Random;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("calc_distance_field")
 */
class CalcDistanceField extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $config = \Drupal::config('views_distance_php.settings');

    $form['latitude'] = array(
      '#type' => 'textfield',
      '#attributes' => array (
        'type' => 'number',
      ),
      '#maxlength' => 10,
      '#title' => $this->t('Latitude Origine'),
      '#description' => $this->t('Latitude de point d\'origine'),
      '#default_value' => $config->get('latitude'),
      '#required' => FALSE,
      '#field_suffix' => '°',
    );
    $form['longitude'] = array(
      '#type' => 'textfield',
      '#attributes' => array (
        'type' => 'number',
      ),
      '#maxlength' => 10,
      '#title' => $this->t('Longitude Origine'),
      '#description' => $this->t('Longitude de point d\'origine'),
      '#default_value' => $config->get('longitude'),
      '#required' => FALSE,
      '#field_suffix' => '°',
    );
    $form['nom'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Nom du point d\'origine'),
      '#description' => $this->t('Nom du point d\'origine'),
      '#default_value' => $config->get('nom'),
      '#required' => FALSE,
      '#size' => 16,
    );

  }

  /**
   * {@inheritdoc}
   */
  public function submitOptionsForm(&$form, FormStateInterface $form_state) {

    $config = \Drupal::config('views_distance_php.settings');
    $values = $form_state->getValues()['options'];

    $config->set('latitude', $values['latitude'])->save();
    $config->set('longitude', $values['longitude'])->save();
    $config->set('nom', $values['nom'])->save();

    parent::submitOptionsForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    if ((array_key_exists('geo_code_1', $this->view->field)) &&
        (array_key_exists('geo_code_2', $this->view->field))) {
      if (! is_null ($this->view->field['geo_code_1']->original_value)) {
        $lat = $this->view->field['geo_code_1']->original_value->__toString();
      }
      else {
        return "";
      }
      if (! is_null ($this->view->field['geo_code_2']->original_value)) {
        $lon = $this->view->field['geo_code_2']->original_value->__toString();
      }
      else {
        return "";
      }

      /* Origine : Montgaillard : 43.124599,0.1083083 */
      $latitude=43.124599;
      $longitude=0.1083083;
      $r_lat1= deg2rad($latitude);
      $r_lon1 = deg2rad($longitude);

      $r_lat2 = deg2rad($lat);
      $r_lon2 = deg2rad($lon);
      $dlo=($r_lon2 - $r_lon1)/2;
      $dla=($r_lat2 - $r_lat1)/2;

      $a = (sin($dla) * sin($dla)) + cos($r_lat1) * cos($r_lat2) * (sin($dlo) * sin($dlo));
      $d = round((2 * atan2 (sqrt($a), sqrt(1 - $a))) * 6378.137, 0);

      return "$d";
    }
    else {
      return "";
    }
  }

}
