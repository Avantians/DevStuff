<?php
/** -------------------------------------------------------------------------
 * This program is Open Source; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * This program is coded in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY
 * @package  CMS
 * @author      Kenwoo - iweb@kenwoo.ca
 * @license    http://creativecommons.org/licenses/by/4.0/ Creative Commons
 *
 * [v02-02/07/2011]:: Set flag, not allow to direct access
 *  ----------------------------------------------------------------------- */
defined( "_VALID_MOS" ) or die( "Your system is not working properly." );
global $base_url;

/** -------------------------------------------------------------------------
 * [00/00/2011]::  Setting Directory
 *  ----------------------------------------------------------------------- */
if ( !defined( "RG_EMULATION" ) ) {
	define( "RG_EMULATION", 0 );		//Off by default for security
}
define( "CHARSET", "utf-8" );
define( "CONFIG_DCOCUMENT_ROOT", CONFIG_DOC_ROOT."/" );
define( "CONFIG_CONSTANTS", CONFIG_DCOCUMENT_ROOT."constants/" );
define( "CONFIG_CLASSES", CONFIG_CONSTANTS."classes/" );
define( "CONFIG_BACKEND", CONFIG_DCOCUMENT_ROOT."backend/" );
define( "CONFIG_BACKEND_CONSTANTS", CONFIG_BACKEND."constants/" );
define( "CONFIG_BACKEND_CLASSES", CONFIG_BACKEND_CONSTANTS."classes/" );
define( "CONFIG_COMMON", CONFIG_DCOCUMENT_ROOT."common/" );
define( "CONFIG_FILES_UPLOAD_IMAGES_TEMP", CONFIG_FILES_UPLOAD_ROOT."temp/" );

/** -------------------------------------------------------------------------
 * E-Mail Transport Method
 * Defines if this server uses a local connection to sendmail or uses an SMTP connection via TCP/IP.
 * Servers running on Windows and MacOS should change this setting to SMTP.
 * OPTIONs: sendmail OR smtp
 * Use MIME HTML When Sending Emails - Send e-mails in HTML format
 *  ----------------------------------------------------------------------- */
define( "EMAIL_TRANSPORT", "sendmail" );

/** -------------------------------------------------------------------------
 * E-Mail Linefeeds
 * Defines the character sequence used to separate mail headers.
 * OPTIONs: LF OR CRLF
 *  ----------------------------------------------------------------------- */
define( "EMAIL_LINEFEED", "LF" );

/** -------------------------------------------------------------------------
 * Use MIME HTML When Sending Emails / Send e-mails in HTML format
 * OPTIONs: true OR false
 *  ----------------------------------------------------------------------- */
define( "EMAIL_USE_HTML", true );

/** -------------------------------------------------------------------------
 * Verify E-Mail Addresses Through DNS
 * Verify e-mail address through a DNS server
 * OPTIONs: true OR false
 *  ----------------------------------------------------------------------- */
define( "ENTRY_EMAIL_ADDRESS_CHECK", false );

/** -------------------------------------------------------------------------
 * Send E-Mails
 * Send out e-mails
 * OPTIONs: true OR false
 *  ----------------------------------------------------------------------- */
define( "SEND_EMAILS", true );

$Config_enable_command_block = 0;

/** -------------------------------------------------------------------------
 * [00/00/2011]::
 *  ----------------------------------------------------------------------- */
$Config_comment_regexp 			= array(
        #Heredoc and Nowdoc syntax
			3 => '/<<<\s*?(\'?)([a-zA-Z0-9]+?)\1[^\n]*?\\n.*\\n\\2(?![a-zA-Z0-9])/siU',
        #phpdoc comments
			4 => '#/\*\*(?![\*\/]).*\*/#sU',
        #Advanced # handling
			2 => "/#.*?(?:(?=\?\>)|^)/smi" );

/** -------------------------------------------------------------------------
 * [00/00/2011]::
 *  ----------------------------------------------------------------------- */
$Config_escape_regexp = array(
        #Simple Single Char Escapes
			1 => "#\\\\[nfrtv\$\"\n\\\\]#i",
        #Hexadecimal Char Specs
			2 => "#\\\\x[\da-fA-F]{1,2}#i",
        #Octal Char Specs
			3 => "#\\\\[0-7]{1,3}#",
        #String Parsing of Variable Names
			4 => "#\\$[a-z0-9_]+(?:\\[[a-z0-9_]+\\]|->[a-z0-9_]+)?|(?:\\{\\$|\\$\\{)[a-z0-9_]+(?:\\[('?)[a-z0-9_]*\\1\\]|->[a-z0-9_]+)*\\}#i",
        #Experimental extension supporting cascaded {${$var}} syntax
			5 => "#\$[a-z0-9_]+(?:\[[a-z0-9_]+\]|->[a-z0-9_]+)?|(?:\{\$|\$\{)[a-z0-9_]+(?:\[('?)[a-z0-9_]*\\1\]|->[a-z0-9_]+)*\}|\{\$(?R)\}#i",
        #Format String support in ""-Strings
			6 => "#%(?:%|(?:\d+\\\\\\\$)?\\+?(?:\x20|0|'.)?-?(?:\d+|\\*)?(?:\.\d+)?[bcdefFosuxX])#" );

/** -------------------------------------------------------------------------
 * [00/00/2011]::  Block list for php module
 *  ----------------------------------------------------------------------- */
$Config_block_list 							= array( "basename", "chgrp", "chmod", "chown", "clearstatcache", "copy", "delete", "dirname", "disk_free_space", "disk_total_space", "diskfreespace", "fclose", "feof", "fflush", "fgetc", "fgetcsv", "fgets", "fgetss", "file_exists", "file_get_contents", "file_put_contents", "file", "fileatime", "filectime", "filegroup", "fileinode", "filemtime", "fileowner", "fileperms", "filesize", "filetype", "flock", "fnmatch", "fopen", "fpassthru", "fputcsv", "fputs", "fread", "fscanf", "fseek", "fstat", "ftell", "ftruncate", "fwrite", "glob", "lchgrp", "lchown", "link", "linkinfo", "lstat", "move_uploaded_file", "opendir", "parse_ini_file", "pathinfo", "pclose", "popen", "readfile", "readdir", "readllink", "realpath", "rename", "rewind", "rmdir", "set_file_buffer", "stat", "symlink", "tempnam", "tmpfile", "touch", "umask", "unlink", "fsockopen", "system", "exec", "passthru", "escapeshellcmd", "pcntl_exec", "proc_open", "proc_close", "mkdir", "rmdir");

/** -------------------------------------------------------------------------
 * [00/00/2011]::
 *  ----------------------------------------------------------------------- */
$Config_notallowed_table					= array( "articles_comment",  "banners_pages",  "categories",  "countries",  "members",  "members_group",  "members_level",  "members_session",  "members_type",  "menu",  "menu_templates",  "menu_type",  "modules",  "modules_pages",  "modules_position",  "opensef",  "sections",  "core_opt" );

$Config_prohibited_string					= array( "#", "*", "NULL", "null", "INTO", "into", "SELECT", "select", "UPDATE", "update", "SET", "set", "WHERE", "where", "VALUES", "values", "LIKE", "like", "AND", "and", "OR", "or", "IS", "is", ';', '!', '"', "'" );
$Config_prohibited_extension			= array( "html", "htm", "php", "phtml", "php3", "inc", "pl", "cgi", "asp" );

$Config_allowed_image_extension	= array( "jpg", "jepg", "gif", "png", "bmp" );
$Config_allowed_docs_extension	= array( "hwp", "pdf", "doc", "docx", "txt", "xlsx", "xls", "csv", "ppt", "pptx" );
$Config_allowed_music_extension	= array( "mp3", "mp4", "avi" );
$Config_allowed_vod_extension		= array( "mp4", "wav", "wmv" );

$Config_entities_match					= array( '.','&quot;','!','@','#','$','%','^','&','*','( ',' )','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','+','~','`','=','’' );
$Config_entities_replace					= array( '','','','','','','','','','','','','','','','','','','','','','','','','','' );
$Config_vod_icon								= "";

/** -------------------------------------------------------------------------
 * [00/00/2011]::
 *  ----------------------------------------------------------------------- */
$Config_freeEmailList						= array( "teampcs.com", "comcast", "yahoo", "hotmail", "live", "gmail", "rediffmail", "aol", "163", "mail.com", "123mail", "21cn", "vsnl", "mynet", "msn", "uol", "126", "terra", "libero", "sify", "indiatimes", "plasa", "prodigy", "gmx", "streamyx", "bigpond", "hanmail", "ig", "cantv", "rediff", "sbcglobal", "t-online", "telkom", "optusnet", "tm", "sancharnet", "tiscali", "tom", "freemail", "rogers", "shaw", "sina", "arcor", "mweb", "seznam", "singnet", "sohu", "sympatico", "bol", "earthlink", "korea", "lycos", "online", "virgilio", "att", "o2", "runbox", "touchtelindia", "usa", "walla", "wanadoo", "web.com", "webmail", "adelphia", "btinternet", "cbn", "daum", "qatar", "argentina", "blueyonder", "europe", "home.com", "iol", "isb", "myjaring", "pchome", "poczta", "yahoomail", "bellsouth", "bluewin", "caramail", "cogeco", "comsats", "coqui", "eyou", "india", "info", "iprimus", "latinmail", "maktoob", "otenet", "planet", "pvfcco", "telnor", "bezeqint", "cox", "direcway", "discoverymail", "dreamwiz", "engineer", "fastmail", "iinet", "netscape", "netspace", "pandora", "skynet", "superonline", "123", "attglobal", "cable.com", "charter", "etang", "fastwebnet", "free", "frontiernet", "gawab", "globe", "hccnet", "inbox", "incnets", "inwind", "juno", "megared", "ms6", "neostrada", "netpci", "netvigator", "netzero", "ntlworld", "ono", "parsonline", "peoplepc", "pop", "racsa", "spymac", "superig", "superlink", "tlen", "verizon", "videotron", "vtown", "woh", "yandex", "2156", "54532", "aanet", "abcnet", "adinet", "adsl", "ahoo", "aim", "aliceadsl", "allstream", "altavista", "andinet", "anet", "aql", "arnet", "asr", "bangkokmail", "batelco", "beotel", "bigpind", "bih", "birch", "biz", "bloomer", "bom3", "cableone", "carib-link", "centennialpr", "centrin", "cg.com", "chello", "chinaren", "cincinnatibell", "comcel", "consultent", "crypthon", "cura", "cybercable", "cybron", "dailytechinfo", "dataxprs", "dcemail", "debitel", "doboj", "dookie", "e-xtra", "eastlink", "eiluae", "eirindia", "elnics", "emcali", "enel", "etapaonline", "eth", "eunet", "excite", "ezaccess", "fchb", "freechal", "freent", "freeserve", "frizz", "fsnet", "fulladsl", "gers11", "giascl01", "giga", "glay", "gvii", "hawaii", "here", "hinet", "homail", "homecall", "hotamil", "hotcom", "hotmai", "hotmal", "hotmial", "hotpop", "hriders", "htomail", "ic24", "iname", "indatimes", "indo", "inet", "infoweb", "insightbb", "interfree", "intergate", "iomind", "isp", "itelefonica", "jarring", "katamail", "knology", "kornet", "lantic", "latnet", "lavalife", "linuxmail", "liwest", "ljosland", "loxinfo", "mail15", "mailcity", "mailto", "maktoop", "malaysia", "mantramail", "menara", "mindspring", "ms1", "ms14", "ms51", "myney", "myway", "naver", "nc.com", "net4u", "netcabo", "netcon", "netian", "netsolir", "nickart", "onlinehome", "operamail", "ozemail", "paran", "pekafrooz", "pipeline", "pobox", "poctza", "portugalmail", "primus.ca", "prtc", "ptt", "qld", "queretaro", "rdslink", "redifmail", "resa", "reymoreno", "rgu", "rinsa", "rr", "sailormoon", "sesz", "sgatuae", "sifymail", "sndt", "speedconnect", "stonline", "suite224", "supaero", "supereva", "swissonline", "talk21", "tee", "telcel", "tele2", "teleline", "telia", "telkomsa", "telmex", "telsur", "telusplanet", "thaimail", "ttnet", "tugamail", "tuktuk", "turkcell", "tutopia", "ultratv", "unet", "verat", "vr-web", "vsni", "wandoo", "westnet", "winning", "wtal", "ya", "yaahoo", "yagoo", "yahool", "yahooo", "yahoooo", "yahopo", "yaoo", "yhaoo", "yshoo", "ystreamyx", "ytu", "zen.com", "rocketmail", "excite", "netcom", "earthlink", "live.com", "sophos", "secude", "pgp", "checkpoint", "utimaco", "guardianedge", "credant", "email", "email.com", "20minutemail.com", "20minutemail", "mailinator", "mailinator.com", "asdf", "asdf.com", "AOL", "AOL.COM" );

$Config_allowedIPs							= array( "204.225.179.*", "208.124.215.*", "10.80.100.*", "99.237.125.*", "204.101.36.*", "204.101.36.*", "174.116.168.*", "204.244.69.*", "76.67.100.*" );
// There is no php closing tag in this file,
// It is intentional because it prevents trailing whitespace problems!