<?php
namespace ElasticActs\App\http;

/**
* Get http header
*/
abstract class getHeader
{
    protected function setHeader( $file = null, $fileExtension )
    {
        header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
        header( "Last-Modified: " . gmdate( 'D, d M Y H:i:s' ) . " GMT" );
        header( "Cache-Control: no-store, no-cache, must-revalidate" );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0, max-age=0", false );
        header( "Cache-Control: private", false ); // required for certain browsers

        if ( ! empty($file) ) {
            $fileExtension = strtolower(substr(strrchr( $file ,"." ), 1));
            // Get contents type
            $tempType      = $this->contentType( $fileExtension );
            header( "Content-Type: $tempType; charset='utf-8'" );
            header( "Content-Transfer-Encoding: binary" );
            header( "Pragma: public" ); // required

            $justFileName  = end(explode( "/", trim( $file )));
            // if the extension is pdf, it will force to open
            if ( $fileExtension == 'pdf' ){
                header( "Content-Disposition: inline; filename=" . $justFileName );
            } else {
                header( "Content-Disposition: attachment; filename=" . $justFileName );
            }

            $fileLocation  = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER . "/". CONFIG_FILES_UPLOAD_ROOT . $file;
            header( "Content-Length: " . filesize($fileLocation) );
            readfile( "".$fileLocation."" );
        } else {
            // Get contents type
            $tempType = $this->contentType( $fileExtension );
            header( "Content-Type: ".$tempType."; charset='utf-8'" );
            header( "Content-Encoding: gzip" );
            header( "Pragma: no-cache" );
        }
    }

    abstract protected function contentType( $fileExtension );
}
