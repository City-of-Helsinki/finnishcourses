<?php

/**
 * @file
 * Contains \Drupal\finnishcourses_rest_api\Controller\RestApiCourse.
 */

namespace Drupal\finnishcourses_rest_api\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller routines for finnishcourses_rest_api Course routes.
 */
class RestApiCourse extends ControllerBase {

  /**
   * Callback for `rest/api/course/create` API method.
   */
  public function post( Request $request ) {

    // This condition checks the `Content-type` and makes sure to 
    // decode JSON string from the request body into array.
    ddl($request->headers->get('Authentication'));
    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
    }

    ddl($request->getContent());

    $response['data'] = 'Some test data to return';
    $response['method'] = 'POST';

    return new JsonResponse( $response );
  }

}