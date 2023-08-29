<?php

namespace Drupal\par_forms;

/**
 * Defines a summary list interface that enables a ParForm plugin to display complex
 * multi-value data in a manageable way using the Summary List component.
 */
interface ParSummaryListInterface {

  /**
   * Get plugin summary list.
   *
   * The summary list helps show the data that has already been added and
   * enables complex form plugins with multiple cardinality to add, change
   * and remove values before submission.
   *
   * A plugin will only support the summary list component if it supports:
   *  - multiple cardinality
   *  - SummaryListInterface
   *
   * @param array $form = []
   *   The form array to add the summary list to.
   *
   * @return mixed
   *   The form elements needed to display the summary list component.
   *   NULL if the component doesn't support a summary list.
   *
   * @see https://github.com/alphagov/govuk-design-system-backlog/issues/21
   * @see https://design-system.service.gov.uk/components/summary-list
   */
  public function getSummaryList(array $form = []): mixed;

}
