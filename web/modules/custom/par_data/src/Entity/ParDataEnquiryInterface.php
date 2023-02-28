<?php

namespace Drupal\par_data\Entity;

use Drupal\comment\CommentInterface;
use Drupal\par_data\Entity\ParDataPersonInterface;
use Drupal\par_data\ParDataException;

/**
 * The interface for PAR entities that share enquiry and response format.
 *
 * @ingroup par_data
 */
interface ParDataEnquiryInterface {

  /**
   * Get the person who created this enquiry.
   *
   * @throws ParDataException
   *   Throws an exception if any of the mandatory primary data is missing.
   *
   * @return ParDataPersonInterface
   *   The enforcement officer who created the enquiry.
   */
  public function creator(): ParDataPersonInterface;

  /**
   * Get the authority that is responsible for sending this enquiry.
   *
   * @throws ParDataException
   *   Throws an exception if any of the mandatory primary data is missing.
   *
   * @return ParDataMembershipInterface
   *   The authority sending the enquiry.
   */
  public function sender(): ParDataMembershipInterface;

  /**
   * Get the authorities that the enquiry is sent to.
   *
   * @return ParDataMembershipInterface[]
   *   An array of authorities.
   */
  public function receiver(): array;

  /**
   * Get the responses to the enquiry.
   *
   * @throws ParDataException
   *   Throws an exception if any of the mandatory primary data is missing.
   *
   * @return CommentInterface[]
   *   An array of comments, or an empty array.
   */
  public function getReplies(): array;
}
