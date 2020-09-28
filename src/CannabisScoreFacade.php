<?php
/**
  * CannabisScoreFacade in Laravel is a class which redirects static 
  * method calls to the dynamic methods of an underlying class
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.0
  */
namespace ResourceInnovation\CannabisScore;

use Illuminate\Support\Facades\Facade;

class CannabisScoreFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cannabisscore';
    }
}
