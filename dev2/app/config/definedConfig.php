<?php
namespace ElasticActs\App\config;

/**
 * Class to return values
 */
trait definedConfig
{

    /**
     * Getting allowed access IP list
     * @return array
     */
    protected static function allowedIPs()
    {
        return [ '204.225.179.*', '208.124.215.*', '10.80.100.*', '99.237.125.*', '204.101.36.*', '204.101.36.*', '174.116.168.*', '204.244.69.*', '76.67.100.*'
        ];
    }

    /**
     * Getting allowed file extension
     * @return array
     */
    protected static function allowedExtenstion()
    {
        return [ 'image' => [ 'jpg', 'jepg', 'gif', 'png', 'bmp' ],
                 'doc'   => [ 'hwp', 'pdf', 'doc', 'docx', 'txt', 'xlsx', 'xls', 'csv', 'ppt', 'pptx' ],
                 'music' => [ 'mp3' ],
                 'vod'   => [ 'mp4', 'avi', 'wav', 'wmv' ]
                ];
    }

    /**
     * Getting Free email address list
     * @return array
     */
    protected static function freeEmails()
    {
        return [ 'teampcs.com', 'comcast', 'yahoo', 'hotmail', 'live', 'gmail', 'rediffmail', 'aol', '163', 'mail.com', '123mail', '21cn', 'vsnl', 'mynet', 'msn', 'uol', '126', 'terra', 'libero', 'sify', 'indiatimes', 'plasa', 'prodigy', 'gmx', 'streamyx', 'bigpond', 'hanmail', 'ig', 'cantv', 'rediff', 'sbcglobal', 't-online', 'telkom', 'optusnet', 'tm', 'sancharnet', 'tiscali', 'tom', 'freemail', 'rogers', 'shaw', 'sina', 'arcor', 'mweb', 'seznam', 'singnet', 'sohu', 'sympatico', 'bol', 'earthlink', 'korea', 'lycos', 'online', 'virgilio', 'att', 'o2', 'runbox', 'touchtelindia', 'usa', 'walla', 'wanadoo', 'web.com', 'webmail', 'adelphia', 'btinternet', 'cbn', 'daum', 'qatar', 'argentina', 'blueyonder', 'europe', 'home.com', 'iol', 'isb', 'myjaring', 'pchome', 'poczta', 'yahoomail', 'bellsouth', 'bluewin', 'caramail', 'cogeco', 'comsats', 'coqui', 'eyou', 'india', 'info', 'iprimus', 'latinmail', 'maktoob', 'otenet', 'planet', 'pvfcco', 'telnor', 'bezeqint', 'cox', 'direcway', 'discoverymail', 'dreamwiz', 'engineer', 'fastmail', 'iinet', 'netscape', 'netspace', 'pandora', 'skynet', 'superonline', '123', 'attglobal', 'cable.com', 'charter', 'etang', 'fastwebnet', 'free', 'frontiernet', 'gawab', 'globe', 'hccnet', 'inbox', 'incnets', 'inwind', 'juno', 'megared', 'ms6', 'neostrada', 'netpci', 'netvigator', 'netzero', 'ntlworld', 'ono', 'parsonline', 'peoplepc', 'pop', 'racsa', 'spymac', 'superig', 'superlink', 'tlen', 'verizon', 'videotron', 'vtown', 'woh', 'yandex', '2156', '54532', 'aanet', 'abcnet', 'adinet', 'adsl', 'ahoo', 'aim', 'aliceadsl', 'allstream', 'altavista', 'andinet', 'anet', 'aql', 'arnet', 'asr', 'bangkokmail', 'batelco', 'beotel', 'bigpind', 'bih', 'birch', 'biz', 'bloomer', 'bom3', 'cableone', 'carib-link', 'centennialpr', 'centrin', 'cg.com', 'chello', 'chinaren', 'cincinnatibell', 'comcel', 'consultent', 'crypthon', 'cura', 'cybercable', 'cybron', 'dailytechinfo', 'dataxprs', 'dcemail', 'debitel', 'doboj', 'dookie', 'e-xtra', 'eastlink', 'eiluae', 'eirindia', 'elnics', 'emcali', 'enel', 'etapaonline', 'eth', 'eunet', 'excite', 'ezaccess', 'fchb', 'freechal', 'freent', 'freeserve', 'frizz', 'fsnet', 'fulladsl', 'gers11', 'giascl01', 'giga', 'glay', 'gvii', 'hawaii', 'here', 'hinet', 'homail', 'homecall', 'hotamil', 'hotcom', 'hotmai', 'hotmal', 'hotmial', 'hotpop', 'hriders', 'htomail', 'ic24', 'iname', 'indatimes', 'indo', 'inet', 'infoweb', 'insightbb', 'interfree', 'intergate', 'iomind', 'isp', 'itelefonica', 'jarring', 'katamail', 'knology', 'kornet', 'lantic', 'latnet', 'lavalife', 'linuxmail', 'liwest', 'ljosland', 'loxinfo', 'mail15', 'mailcity', 'mailto', 'maktoop', 'malaysia', 'mantramail', 'menara', 'mindspring', 'ms1', 'ms14', 'ms51', 'myney', 'myway', 'naver', 'nc.com', 'net4u', 'netcabo', 'netcon', 'netian', 'netsolir', 'nickart', 'onlinehome', 'operamail', 'ozemail', 'paran', 'pekafrooz', 'pipeline', 'pobox', 'poctza', 'portugalmail', 'primus.ca', 'prtc', 'ptt', 'qld', 'queretaro', 'rdslink', 'redifmail', 'resa', 'reymoreno', 'rgu', 'rinsa', 'rr', 'sailormoon', 'sesz', 'sgatuae', 'sifymail', 'sndt', 'speedconnect', 'stonline', 'suite224', 'supaero', 'supereva', 'swissonline', 'talk21', 'tee', 'telcel', 'tele2', 'teleline', 'telia', 'telkomsa', 'telmex', 'telsur', 'telusplanet', 'thaimail', 'ttnet', 'tugamail', 'tuktuk', 'turkcell', 'tutopia', 'ultratv', 'unet', 'verat', 'vr-web', 'vsni', 'wandoo', 'westnet', 'winning', 'wtal', 'ya', 'yaahoo', 'yagoo', 'yahool', 'yahooo', 'yahoooo', 'yahopo', 'yaoo', 'yhaoo', 'yshoo', 'ystreamyx', 'ytu', 'zen.com', 'rocketmail', 'excite', 'netcom', 'earthlink', 'live.com', 'sophos', 'secude', 'pgp', 'checkpoint', 'utimaco', 'guardianedge', 'credant', 'email', 'email.com', '20minutemail.com', '20minutemail', 'mailinator', 'mailinator.com', 'asdf', 'asdf.com', 'AOL', 'AOL.COM'
        ];
    }

    /**
     * Getting prohited information
     * @return array
     */
    protected static function prohibiteds()
    {
        $prohibited['dblist'] = [ "basename", "chgrp", "chmod", "chown", "clearstatcache", "copy", "delete", "dirname", "disk_free_space", "disk_total_space", "diskfreespace", "fclose", "feof", "fflush", "fgetc", "fgetcsv", "fgets", "fgetss", "file_exists", "file_get_contents", "file_put_contents", "file", "fileatime", "filectime", "filegroup", "fileinode", "filemtime", "fileowner", "fileperms", "filesize", "filetype", "flock", "fnmatch", "fopen", "fpassthru", "fputcsv", "fputs", "fread", "fscanf", "fseek", "fstat", "ftell", "ftruncate", "fwrite", "glob", "lchgrp", "lchown", "link", "linkinfo", "lstat", "move_uploaded_file", "opendir", "parse_ini_file", "pathinfo", "pclose", "popen", "readfile", "readdir", "readllink", "realpath", "rename", "rewind", "rmdir", "set_file_buffer", "stat", "symlink", "tempnam", "tmpfile", "touch", "umask", "unlink", "fsockopen", "system", "exec", "passthru", "escapeshellcmd", "pcntl_exec", "proc_open", "proc_close", "mkdir", "rmdir"
        ];

        $prohibited['table'] = [ 'articles_comment',  'banners_pages',  'categories',  'countries',  'members',  'members_group',  'members_level',  'members_session',  'members_type',  'menu',  'menu_templates',  'menu_type',  'modules',  'modules_pages',  'modules_position',  'opensef',  'sections',  'core_opt'
        ];

        $prohibited['string'] = [ '#', '*', 'NULL', 'null', 'INTO', 'into', 'SELECT', 'select', 'UPDATE', 'update', 'SET', 'set', 'WHERE', 'where', 'VALUES', 'values', 'LIKE', 'like', 'AND', 'and', 'OR', 'or', 'IS', 'is', ';', '!', '"', "'"
        ];

        $prohibited['extension'] = [ 'html', 'htm', 'php', 'phtml', 'php3', 'inc', 'pl', 'cgi', 'asp', 'js'
        ];

        return $prohibited;
    }
}
