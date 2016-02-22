<?php
namespace serialGenerator\allFiles;

/**
 * Inherit a set of base functionality to child
 */
abstract class numMaker
{
    protected $uniqueSerial;
    protected $santizedEmail;
    protected $uniqueEmail;
    protected $tempUniqueNumber;

    /**
     * Generate a better random numbers as 5 digits
     *
     * @param  string $eaddress Email address
     * @param  string $fn       File Name
     * @return string           Unique Serial number or message
     */
    protected function randomSerial($eaddress, $fn)
    {
        // Basic santize email
        $this->santizedEmail    = $this->basicSantize($eaddress, 'email');
        // MD5 hash email
        $this->uniqueEmail      = strtoupper(md5($this->santizedEmail));
        // Get random unqiue number
        $this->tempUniqueNumber = substr(number_format(time() * rand(),0,'',''), 0, 8);
        // Get full Serial number
        $this->uniqueSerial     = $this->uniqueEmail . $this->tempUniqueNumber;
        // Santize file name and add file extension
        $fullFileName           = $this->basicSantize($fn);
        // Get all of contents from the file
        $getContents            = file_get_contents($fullFileName);

        // Checking the email has been used to generate token or not
        if (!preg_match("/". $this->uniqueEmail ."/", $getContents)) {
            // Save Serial number
            $this->saveToFile($fullFileName, $this->uniqueSerial);
            $retruning = "<div class=\"alert alert-info\" role=\"alert\">Please keep your token safe.<br/><br/>Email: %s<br/>Token: %s</div>";

            return sprintf($retruning, $this->santizedEmail, $this->uniqueSerial);
        } else {
            return "<div class=\"alert alert-warning\" role=\"alert\">This email has been used.</div>";
        }
    }

    /**
     * Write the contects to the file
     *
     * @param  string  $fileName
     * @param  intiger $uniqueNumber
     * @return string                   error message
     */
    public function saveToFile($fileName, $uniqueNumber)
    {
        $passedNumber = $uniqueNumber."\n";
        try {
            // Write the contents to the file,
            $fh = fopen($fileName, 'a');
            fwrite($fh, $passedNumber);
            fclose($fh);
        } catch (Exepction $e) {
            return $e->getMessage();
        }
    }

    /**
     * Basic Santizing string for file name and add file extension as text
     * @param  string $targetedFile
     * @return string               santized file name witn extension as text
     */
    public function basicSantize($targetedTxt, $triger = null)
    {
        if ($triger === 'email') {
            return filter_var($targetedTxt, FILTER_SANITIZE_EMAIL);
        } elseif ($triger === 'str') {
            return filter_var($targetedTxt, FILTER_SANITIZE_STRING);
        } else {
            return filter_var($targetedTxt, FILTER_SANITIZE_STRING).".txt";
        }
    }

    /**
     * Read entire file into an array
     * @param  string $targetedFile
     * @return array
     */
    public function fileToarray($targetedFile)
    {
        // FILE_IGNORE_NEW_LINES avoid to add newline at the end of each array element
        // FILE_SKIP_EMPTY_LINES to Skip empty lines
        return file($targetedFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}
