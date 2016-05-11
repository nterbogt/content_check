<?php

interface ContentCheckInterface {

  public static function isApplicable($entity_type);

  public function check($entity_type, $entity);

}
