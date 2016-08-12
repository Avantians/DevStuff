<?php
namespace ElasticActs\Constants\libraries;

/**
 * Class handle Images
 */
class getHandleImages
{
    public function __construct()
    {

    }


/** -------------------------------------------------------------------------
 * [00/00/2009]::
 *  ----------------------------------------------------------------------- */
function getThumb_image( $path, $src, $alt = '', $width = '', $height = '', $parameters = '' ){

    $image_size = @getimagesize( $src );

    if ( ( ( $src == '' ) || ( $src == DIR_IMAGES ) ) ){
      return false;
    }

    //To get FULL FILE NAME only
    $file = substr( getParseInputFieldData( $src, array( '"' => '&quot;' ) ), strrpos( getParseInputFieldData( $src, array( '"' => '&quot;' ) ), "/" )+1 );

    //To get file extension ONLY >> strtoupper( substr( $src,strrpos( $src,"." ) ) );
    if( $image_size[0] > $width && file_exists( $path.$file ) ){

        if ( $width > 0 ) $filenamesize .= $width . '_';
        if ( $height > 0 ) $filenamesize .= $height . '_';

        if ( file_exists( $path.'temp/'.$filenamesize.$file ) ){
            return '<img src="' . $path.'temp/' .$filenamesize. $file . '" border="0" alt="' . getParseInputFieldData( $alt, array( '"' => '&quot;' ) ) . '" ' . $parameters . ' />';

        }
        else{
            $thumb = new thumbnail( getParseInputFieldData( $src, array( '"' => '&quot;' ) ) );

            if ( ! $thumb->img['src'] ){
                // return tep_image_OLD( $src, $alt, $width, $height, $params );
            }

            // $thumb->size_width( $width );
            // $thumb->size_height( $height );
            $thumb->size_auto( $height );
            $thumb->save( $path.'temp/'.$filenamesize.$file );

            return '<img src="' . $path.'temp/' . $filenamesize . $file . '" border="0" alt="' . getParseInputFieldData( $alt, array( '"' => '&quot;' ) ) . '" ' . $parameters . ' />';
        }
    }
    else{
      //alt is added to the img tag even if it is null to prevent browsers from outputting
      //the image filename as default
        $image = '<img src="' . getParseInputFieldData( $src, array( '"' => '&quot;' ) ) . '" border="0" alt="' . getParseInputFieldData( $alt, array( '"' => '&quot;' ) ) . '"';

        if ( getCheckNullorNot( $alt ) ){
            $image .= ' title="' . getParseInputFieldData( $alt, array( '"' => '&quot;' ) ) . ' "';
        }

        if ( getCheckNullorNot( $parameters ) ) $image .= ' ' . $parameters;

        $image .= ' />';

        return $image;
    }
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Display an image for Backend
 *  ----------------------------------------------------------------------- */
function getDisplayImgBK( $filename, $maxWidth, $alttxt="", $displayTxt = true ){
        global $Config_allowed_image_extension;

        if( !empty( $filename ) ){
            $file_array     = explode( ";", $filename );
            if( count( $file_array ) > 0 ){
                for( $k=0; $k < count( $file_array ); $k++ ){
                    $extension_array[$k] = explode( ".", $file_array[$k] );
                    $fileextension = end($extension_array[$k]);
                    if(in_array($fileextension,$Config_allowed_image_extension)){

                        $image_path[$k] = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k];
                        if( file_exists( $image_path[$k] ) ){
                            list( $width, $height )= getimagesize( $image_path[$k] );

                            if( $width > $maxWidth ){
                                $percent_resizing = round( ( $maxWidth / $width ) * 100 );
                                $new_width = $maxWidth;
                                $new_height  = round( ( $percent_resizing / 100 )  * $height );
                                $displayimg .= "\n<img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" width=\"{$new_width}\" height=\"{$new_height}\" alt=\"{$alttxt}_w{$width}_h{$height}\"/>\n<br/>";
                                if( $displayTxt === true ){
                                    $displayimg .= "\n<small>This image has been resized to display.</small>\n<br/>\n";
                                }

                            }
                            else{
                                $displayimg .= "\n<img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" width=\"{$width}\" height=\"{$height}\" alt=\"{$alttxt}\"/>\n<br/>\n";
                            }

                        }
                        else{
                            $displayimg .= "<small>\"".$file_array[$k]."\"  does not exist in the folder.</small>";
                        }
                    }
                    else {
                        $displayimg = "";
                    }
                }
            }
        }
        else{
            $displayimg = "";
        }

    return $displayimg;
}

/** -------------------------------------------------------------------------
 * [00/00/2009]::Display an images for Frontend
 *  ----------------------------------------------------------------------- */
function getDisplayImg( $filename, $maxWidth, $nailthumb = false, $alttxt="", $useDiv = true, $extraClass="" ){
        global $Config_allowed_image_extension;

        if ( !empty( $filename ) ){
            $file_array     = explode( ";", $filename );
            if ( count( $file_array ) > 0 ){
                for ( $k=0; $k < count( $file_array ); $k++ ){
                    $extension_array[$k] = explode( ".", $file_array[$k] );
                    $fileextension = end($extension_array[$k]);
                    if ( in_array($fileextension,$Config_allowed_image_extension) ){
                            $image_path[$k] = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k];
                            if ( file_exists( $image_path[$k] ) ){
                                list( $width, $height )= getimagesize( $image_path[$k] );
                                if ( $width >= $maxWidth && 0 != $maxWidth ){
                                    //$widthsize    = ( $nailthumb == true ) ? round( ( $width / $maxWidth ) * 100 ) : $width;
                                    $widthsize  = $maxWidth;
                                }
                                else {
                                    $widthsize  = $width;
                                }

                                $heightsize = floor( ( $height / $width ) * $widthsize );
                                $extraClass = !empty( $extraClass ) ? " class=\"{$extraClass}\"" : "";
                                if( $nailthumb == true ){
                                        $displayimg .= "<img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" alt=\"{$alttxt}\"{$extraClass} data-width=\"".$width."\"/>\n";
                                }
                                else {
                                        if ( $useDiv == true ){
                                            $displayimg .= "<div{$extraClass} style=\"margin:0 auto; max-width:{$widthsize}px;\"><img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" width=\"{$widthsize}\" height=\"{$heightsize}\" alt=\"{$alttxt}\" data-width=\"".$width."\"/></div>\n";
                                        }
                                        else {
                                            $displayimg .= "<img src=\"". CONFIG_SITE_URL ."/". CONFIG_FILES_UPLOAD_ROOT . $file_array[$k] ."\" alt=\"{$alttxt}\"{$extraClass} data-width=\"".$width."\" style=\"margin:0 auto; max-width:{$widthsize}px;\"/>\n";
                                        }
                                }
                            }
                            else {
                                $displayimg .= "<small>No file <br/>in the folder</small>";
                            }
                    }
                    else {
                        $displayimg = "";
                    }
                }
            }
        }
        else {
            $displayimg = "";
        }

    return $displayimg;
}

/** -------------------------------------------------------------------------
 * [05/02/2013]::Display an banner for pages
 *  ----------------------------------------------------------------------- */
function getDisplayBanner( $filename, $fulltxt="", $alttxt="", $urltxt="", $shtmlTitle="", $target_window="", $extraClass="", $frontCheck, $asImg = false){
    global $base_url;

        if( !empty( $filename ) ){
            $image_path = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER ."/". CONFIG_FILES_UPLOAD_ROOT . $filename;
            $image_url      = $base_url."/". CONFIG_FILES_UPLOAD_ROOT . $filename;

            if( file_exists( $image_path ) ){
                list( $width, $height ) = getimagesize( $image_path );
                $imgs           = "<img src=\"$image_url\" alt=\"$alttxt\" width=\"$width\" height=\"$height\" data-width=\"$width\">";
                $extraClass = !empty( $extraClass ) ? " class=\"". $extraClass ."\"" : "";

                //Front page banners
                if( $frontCheck == 1 ){
                        $Afulltxt = _getCheckNullorNot( $fulltxt ) ? "<h3>". $fulltxt ."</h3>" : "";
                        if( _getCheckNullorNot( $urltxt ) ){
                            //$displayimg .= "\t<li".$extraClass ."><a href=\"". $this->getProperLink($urltxt) ."\" title=\"".$shtmlTitle."\" target=\"".$target_window."\">".$imgs . $Afulltxt ."</a></li>";
                            $displayimg .= "<div class=\"slides\" style=\"background-image:url('". $image_url ."');\"><div class=\"container text-center\"><a href=\"". $this->getProperLink($urltxt) ."\" title=\"".$shtmlTitle."\" target=\"".$target_window."\">".$imgs . $Afulltxt ."</a></div></div>";
                        }
                        else {
                            $displayimg .= "<div class=\"slides\" style=\"background-image:url('". $image_url ."');\"><div class=\"container text-center\">". $Afulltxt ."</div></div>";
                        }
                }
                //Other pages' banners
                else {
                        $Afulltxt = _getCheckNullorNot( $fulltxt ) ? "<h3 class=\"banner-heading\">". $fulltxt ."</h3>" : "";
                    if( $asImg == true) {
                        $displayimg .= "<div". $extraClass ." style=\"max-width: ".$width."px;\">".$imgs . $Afulltxt ."</div>\n";
                    }
                    else{
                        //$displayimg .= "\t<li". $extraClass ." style=\"background-image: url('".$image_url."');\">". $Afulltxt ."</li>";
                        $displayimg .= "<div class=\"slides4page\" style=\"background-image:url('". $image_url ."');\"><div class=\"container text-center\">". $Afulltxt ."</div></div>";
                    }
                }
            }
            else{
                $displayimg .= "<small>No Image</small>";
            }
        }
        else{
            $displayimg = "";
        }

    return $displayimg;
}

}
