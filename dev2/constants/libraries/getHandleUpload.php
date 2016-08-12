<?php
namespace ElasticActs\Constants\libraries;

/**
 * Class handle Images
 */
class getHandleUpload
{
    public function __construct()
    {

    }

    /**
     * To assign a file into proper folder
     */
    public function getDestinationFolder ( $file_name )
    {
        // To check the file is okay to upload or not base on the extension of file
        $filename   = explode( ".", $file_name );
        $extension  = $filename[sizeof( $filename )-1];

        if ( in_array( $extension,$Config_prohibited_extension ) ){
            $error_message[] = "NOT_ALLOWED_FILE_FORMAT";
            $error_flag      = true;

            return $error_message;
        } elseif ( in_array( $extension,$Config_allowed_image_extension ) ){
            $file_folder = CONFIG_FILES_UPLOAD_IMAGES;
        } elseif ( in_array( $extension,$Config_allowed_docs_extension ) ){
            $file_folder = CONFIG_FILES_UPLOAD_DOCS;
        } elseif ( in_array( $extension,$Config_allowed_vod_extension ) ){
            $file_folder = CONFIG_FILES_UPLOAD_VOD;
        } elseif ( in_array( $extension,$Config_allowed_music_extension ) ){
            $file_folder = CONFIG_FILES_UPLOAD_MUSIC;
        }

        return $file_folder;
    }

    /** -------------------------------------------------------------------------
     * [00/00/2009]::Replace
     *  ----------------------------------------------------------------------- */
    public function changFileName( $filename )
    {
        $temp = strtolower( $filename );

        for ( $i=0; $i< strlen( $temp ); $i++ ){
            if ( !preg_match( '/[^0-9a-z\.\_\-]/i', $temp[$i] ) ){
                $result = $result . $temp[$i];
            } else {
                $result = $result . $temp[$i];
            }
        }

        return $result;
    }

    /** -------------------------------------------------------------------------
     * [00/00/2009]::
     *  ----------------------------------------------------------------------- */
    public function getUploadMultiFile ( $aFILES, $thumb = true )
    {
        global $Bon_db, $Config_prohibited_extension, $Config_allowed_image_extension, $Config_allowed_docs_extension, $Config_allowed_vod_extension, $Config_allowed_music_extension;

        for( $i=0; $i < count( $aFILES[name] ); $i++ ){
            $upload_file_name[$i] = strtolower( $aFILES[name][$i] );
            $upload_file_name[$i] = str_replace( " ","_",$upload_file_name[$i] );
            $upload_file_name[$i] = str_replace( "-","_",$upload_file_name[$i] );

            //To check the file is okay to upload or not base on the extension of file
            $filename[$i]               = explode( ".", $upload_file_name[$i] );
            $extension[$i]          = end( $filename[$i] );

            $upload_file[$i]            = $aFILES[tmp_name][$i];
            $upload_file_size[$i]   = $aFILES[size][$i];
            $upload_file_type[$i]   = $aFILES[type][$i];

            //To check Floder Permission
            $destination_folder = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER . "/". CONFIG_FILES_UPLOAD_ROOT . getDestinationFolder( $upload_file_name[$i] );
            if ( is_dir( $destination_folder ) && $error_flag == false ){
                @chmod( $destination_folder,0777 ); //Force to change the folder's Permission

                $destination_subfolder = $_SERVER['DOCUMENT_ROOT'] . UNDER_SUBFOLDER  ."/". CONFIG_FILES_UPLOAD_ROOT . getDestinationFolder($upload_file_name[$i]) . date("Ym");
                if(!is_dir($destination_subfolder)){
                    @mkdir($destination_subfolder, 0777, true);
                }
            } else {
                $error_message[] = "DIRECTORY_NOT_EXIST_OR_IMPROPER_FOLDER :: ".$destination_folder;
                $error_flag = true;
            }

            //To check there is same file name or not
            $destination[$i] = $destination_subfolder ."/". $upload_file_name[$i];
            if ( file_exists( $destination[$i] ) && $error_flag == false ){
                $destination[$i] = $destination_subfolder ."/". $filename[$i][0] ."_". time().".".$filename[$i][1];
                $upload_file_name[$i] = $filename[$i][0] ."_". time().".".$filename[$i][1];
            }

            //To save a file into folder
            if ( !@move_uploaded_file( $upload_file[$i], $destination[$i] ) && $error_flag == false ){
                $error_message[] = "ACCESS_DENIED_TO_COPY";
                $error_message[] = "Error moving uploaded file ".$upload_file[$i]." to the ". $destination[$i];
                $error_message[] = "Check the directory permissions for ". getDestinationFolder( $upload_file_name[$i] ) ."( must be 777 )!";
                $error_flag      = true;
            }

            //After saving into proper folder, delete the file from buffer.
            if( !@unlink( $upload_file[$i] ) && $error_flag == false ){
                $error_message[] = "ACCESS_DENIED_TO_DELETE_TMP_FILE";
                $error_flag = true;
            }

            if ( $thumb === true ){
                if (in_array($extension,$Config_allowed_image_extension)){
                    //ob_end_flush();
                    $src = $destination.$upload_file_name;
                        filesize($src);
                    $image_size = @getimagesize($src);
                    if ( $image_size[0] > 700 ){
                        $newName = $filename[0]."_700.".$filename[1];
                        $thumb = new thumbnail($src);
                        $thumb->size_width(700);
                        $thumb->save($destination.$newName);
                        !@unlink($destination.$upload_file_name);

                        $upload_file_name = $newName;
                    }
                }
            }

            $uploading_file['name'][$i] = getDestinationFolder( $upload_file_name[$i] ) . date("Ym") ."/". $upload_file_name[$i];
            $uploading_file['type'][$i] = $extension[$i];   //$upload_file_type;
            $uploading_file['size'][$i] = $upload_file_size[$i];
        }

        return $uploading_file;
    }

    /**
     * Get proper file size
     */
    public function getSizeFile( $url )
    {
        if ( substr( $url,0,4 )=='http' ){
            $x = array_change_key_case( get_headers( $url, 1 ),CASE_LOWER );
            if ( strcasecmp( $x[0], 'HTTP/1.1 200 OK' ) != 0 ){
                $x = $x['content-length'][1];
            } else{
                $x = $x['content-length'];
            }
        } else {
            $x = @filesize( $url );
        }

        return $x;
    }

    /**
     * Get proper file size
     */
    public function makeClickable( $text )
    {
        $pattern  = '#\b(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#';
        return preg_replace_callback($pattern, 'auto_link_text_callback', $text);
    }

    /**
     * Remove Directory and files
     */
    public function rmDir( $dir )
    {
        $structure = glob(rtrim($dir, "/").'/*');
        if (is_array($structure)){
            foreach ($structure as $file){
                if (is_dir($file)) {
                    rmDir($file);
                } elseif (is_file($file)){
                    @unlink($file);
                }
            }
        }

        $folders = explode( "/", rtrim($dir, "/"));
        if( end($folders) != "thumbs" ){
            @rmdir($dir);
        }
    }
}
