<?php
namespace serialGenerator\allFiles;

use serialGenerator\allFiles\numMaker;
/**
 * Validation class
 *
 */
class validation extends numMaker
{
    private $targetedName;

    public function __construct()
    {   // Set default file name
        $this->targetedName = 'serials';
    }

    public function getValidation($email, $sn = null)
    {
        // Basic santize email and serial
        $this->santizedEmail    = numMaker::basicSantize($email, 'email');
        $this->santizedSerial   = numMaker::basicSantize(strtoupper(trim($sn)), 'str');
        // MD5 hash email
        $this->uniqueEmail      = strtoupper(md5($this->santizedEmail));
        // Santize file name and add file extension
        $fullFileName           = numMaker::basicSantize($this->targetedName);
        // Get all of contents from the file
        $getContents            = file_get_contents($fullFileName);

        // Checking the length of typed serial number
        if (strlen($this->santizedSerial) != 40) {
            return "<div class=\"alert alert-danger\" role=\"alert\">The token is not valid.</div>";

        // Checking you have that serial number in the file or not
        } elseif (!preg_match("/". $this->santizedSerial ."/", $getContents)) {
            return "<div class=\"alert alert-danger\" role=\"alert\">You do not have your token.</div>";

        // Checking the typed email has been used to generated Serial number or not
        } elseif (!preg_match("/". $this->uniqueEmail ."/", $getContents)) {
            return "<div class=\"alert alert-danger\" role=\"alert\">We don't find this email in our database.</div>";

        // Checking the email and serial number is same or not
        } elseif ($this->uniqueEmail !== substr($this->santizedSerial, 0, -8)) {
            return "<div class=\"alert alert-danger\" role=\"alert\">The token is not valid.</div>";

        // You have valid one
        } else {
            return "<div class=\"alert alert-success\" role=\"alert\">You have the valid token for " . $this->santizedEmail . "<br/>Token: " . $this->santizedSerial . "</div>";
        }
    }


}
