<?php
function genUUID()
{
        $strAction     = getAction();
        $strServerAddr = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
        $longIp        = sprintf('%u', ip2long($strServerAddr));
        $intfd         = posix_getpid(); // getmypid()
        $strSeed       = sprintf('%s-%s-%s-%s', $longIp, $intfd, md5($strAction), rand(100, 999));
        $strUniqid     = md5(uniqid($strSeed, true));
        return getFormatUUID($strUniqid);
}
function getAction()
{
        $strAction = '';
        if ('cli' === PHP_SAPI) {
                $srtAction = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : $_SERVER['argv'][0];
                $srtAction = 'cli-' . $srtAction;
        } else {
                $uri = strstr($_SERVER['REQUEST_URI'], '?', true);
                $srtAction = false === $uri ? $_SERVER['REQUEST_URI'] : $uri;
        }
        return $strAction;
}
function getFormatUUID($_str32Uniqid)
{
        if (!isset($_str32Uniqid[31])) {
                return '';
        }
        return sprintf('%s-%s-%s-%s-%s',
                        substr($_str32Uniqid, 0, 8),
                        substr($_str32Uniqid, 8, 4),
                        substr($_str32Uniqid, 12, 4),
                        substr($_str32Uniqid, 16, 4),
                        substr($_str32Uniqid, 20, 12));
}
echo genUUID();
