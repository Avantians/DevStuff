<?php
namespace serialGenerator\allFiles;

use serialGenerator\allFiles\numMaker;
/**
 * Serial class
 * Getting unique serial number
 */
class serial extends numMaker
{
    private $targetedName;

    public function __construct()
    {   // Set default file name
        $this->targetedName = 'serials';
    }

    public function setSerial($em)
    {
       return numMaker::randomSerial($em, $this->targetedName);
    }
}
