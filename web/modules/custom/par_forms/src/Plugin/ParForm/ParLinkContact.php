<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\comment\CommentInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_flows\ParFlowException;
use Drupal\par_forms\ParEntityMapping;
use Drupal\par_forms\ParFormPluginBase;
use Drupal\user\Entity\Role;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * User details display plugin.
 *
 * @ParForm(
 *   id = "link_contact",
 *   title = @Translation("Link contact to user.")
 * )
 */
class ParLinkContact extends ParFormPluginBase {

  /**
   * @return DateFormatterInterface
   */
  protected function getDateFormatter() {
    return \Drupal::service('date.formatter');
  }

  /**
   * {@inheritdoc}
   */
  public function loadData($cardinality = 1) {
    if ($par_data_person = $this->getFlowDataHandler()->getParameter('par_data_person')) {
      $email = $par_data_person->getEmail();
    }
    else {
      $cid_contact_details = $this->getFlowNegotiator()->getFormKey('contact_details');
      $email = $this->getFlowDataHandler()->getDefaultValues('email', NULL, $cid_contact_details);
    }

    // If an account can be found that matches by e-mail address then we should use this.
    if (!empty($email) && $user = current($this->getParDataManager()->getEntitiesByProperty('user', 'mail', $email))) {
      $this->getFlowDataHandler()->setFormPermValue("user_id", $user->id());
    }

    parent::loadData();
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = [], $cardinality = 1) {
    $user_id = $this->getFlowDataHandler()->getDefaultValues('user_id', NULL);

    // If there is no user skip on to the invitation stage.
    if ($user_id) {
      $this->getFlowDataHandler()->setTempDataValue('user_id', $user_id);
    }

    // There is no display for this form, it always redirects to the next step once the user has been selected.
    $url = $this->getUrlGenerator()->generateFromRoute($this->getFlowNegotiator()->getFlow()->getNextRoute('next'), $this->getRouteParams());
    return new RedirectResponse($url);
  }

  /**
   * Return no actions for this plugin.
   */
  public function getElementActions($cardinality = 1, $actions = []) {
    return $actions;
  }

  /**
   * Return no actions for this plugin.
   */
  public function getComponentActions($actions = [], $count = NULL) {
    return $actions;
  }
}
