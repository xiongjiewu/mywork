<?php
class Globalfunction extends CI_Model {

    function __construct()
    {
        parent::__construct();

    }
    public function ubb2html($html, $title = null)
    {
        global $bUbb2htmlFunctionInit;

        if (!$bUbb2htmlFunctionInit) {

            function match_tags($html, $reg)
            {
                if (!preg_match_all('/(' . $reg . ')/is', $html, $matches, PREG_OFFSET_CAPTURE)) return array();
                foreach ($matches[0] as $k => $v) {
                    if (substr($v[0], 1, 1) == '/') {
                        $v[2] = 'e';
                    } else {
                        $v[2] = 's';
                    }
                    $tags[$v[1]] = $v;
                }
                return $tags;
            }

            function process_tags($tags)
            {
                if (empty($tags)) return array();
                $stack = $replace = array();
                foreach ($tags as $tag) {
                    if (!empty($stack)) {
                        $last = end($stack);
                        if ($last[2] == 's' && $tag[2] == 'e') {
                            $replace[$tag[1]] = $tag[0];
                            $replace[$last[1]] = $last[0];
                            array_pop($stack); //出栈
                        } else {
                            $stack[] = $tag; //入栈
                        }
                    } else {
                        $stack[] = $tag; //入栈
                    }
                }
                krsort($replace);
                return $replace;
            }

            function simple_replace($html, $start, $text)
            {
                return substr_replace($html, str_replace(array('[', ']'), array('<', '>'), $text), $start, mb_strlen($text));
            }

            function getSizeName($match)
            {
                $arrSize = array('8pt', '10pt', '12pt', '14pt', '18pt', '24pt', '36pt');
                if ($match[1] >= 1 && $match[1] <= 7) {
                    $res = '<span style="font-size:' . $arrSize[$match[1] - 1] . ';">';
                } else {
                    $res = '<span style="font-size:' . $match[1] . ';">';
                }
                return $res;
            }

            function getImg($match)
            {
                $p1 = $match[1];
                $p2 = $match[2];
                $p3 = $match[3];
                $src = $match[4];
                $a = $p3 ? $p3 : (!is_numeric($p1) ? $p1 : '');
                return '<img src="' . $src . '"' . (is_numeric($p1) ? ' width="' . $p1 . '"' : '') . (is_numeric($p2) ? ' height="' . $p2 . '"' : '') . ($a ? ' align="' . $a . '"' : '') . ' border="0" alt="' . $title . '" onload="if(this.width>700){this.width=700;}"/>';
            }

            function getFlash($match)
            {
                $w = $match[1];
                $h = $match[2];
                $url = $match[3];
                if (!$w) $w = 550;
                if (!$h) $h = 400;
                return '<embed type="application/x-shockwave-flash" src="' . $url . '" wmode="opaque" quality="high" bgcolor="#ffffff" menu="false" play="true" loop="true" width="' . $w . '" height="' . $h . '" />';
            }

            function getMedia($match)
            {
                $w = $match[1];
                $h = $match[2];
                $play = $match[3];
                $url = $match[4];
                if (!$w) $w = 550;
                if (!$h) $h = 400;
                return '<embed type="application/x-mplayer2" src="' . $url . '" enablecontextmenu="false" autostart="' . ($play == '1' ? 'true' : 'false') . '" width="' . $w . '" height="' . $h . '" />';
            }

            function getTable($match)
            {
                return '<table' . (isset($match[1]) ? ' width="' . $match[1] . '"' : '') . (isset($match[2]) ? ' bgcolor="' . $match[2] . '"' : '') . '>';
            }

            function getTR($match)
            {
                return '<tr' . (isset($match[1]) ? ' bgcolor="' . $match[1] . '"' : '') . '>';
            }

            function getTD($match)
            {
                $col = isset($match[1]) ? $match[1] : 0;
                $row = isset($match[2]) ? $match[2] : 0;
                $w = isset($match[3]) ? $match[3] : null;
                return '<td' . ($col > 1 ? ' colspan="' . $col . '"' : '') . ($row > 1 ? ' rowspan="' . $row . '"' : '') . ($w ? ' width="' . $w . '"' : '') . '>';
            }

            function getUL($match)
            {
                $str = '<ul';
                if (isset($match[1])) $str .= ' type="' . $match[1] . '"';
                return $str . '>';
            }

            function fixText($match)
            {
                $text = $match[2];
                $text = preg_replace("/\t/", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $text);
                $text = preg_replace("/ /", '&nbsp;', $text);
                return $match[1] . $text;
            }

            function beforeQuote($sHtml)
            {
                global $quoteMatch;
                preg_match_all("/\[quote\]([\s\S]*?)\[\/quote\]/i", $sHtml, $m);
                $quoteMatch = $m[0];
                foreach ($quoteMatch as $k => $v) {
                    $quoteMatch[$k] = preg_replace("/\[quote\]([\s\S]*?)\[\/quote\]/i", '<div class="quote"><div class="quote_header"><span style="font-size:14px;font-weight:bold">引用:</span></div><div class="quote_content">$1</div></div>', $v);
                    $sHtml = str_replace($v, '#$@quotemsg@$#' . $k, $sHtml);
                }
                return $sHtml;
            }

            function afterQuote($sHtml)
            {
                global $quoteMatch;
                foreach ($quoteMatch as $k => $v) {
                    $sHtml = str_replace('#$@quotemsg@$#' . $k, $v, $sHtml);
                }
                return $sHtml;
            }

            function getUrl($match)
            {
                //外链允许白名单 by xingjiewu
                if (preg_match('/(dianying8.com|dianyingba.com)/i', $match[1])) {
                    return '<a href="' . $match[1] . '" target="_blank">' . ($match[2] ? $match[2] : $match[1]) . '</a>';
                } else {
                    return $match[2] ? $match[2] : $match[1];
                }
            }
        }

        $html = preg_replace(array("/&/", "/</", "/>/", "/\r?\n/"), array('&amp;', '&lt;', '&gt;', '<br />'), $html);

        if (strstr($html, '[/b]')) {
            $tags = process_tags(match_tags($html, '\[b\]|\[\/b\]'));
            foreach ($tags as $k => $v) $html = simple_replace($html, $k, $v);
        }

        if (strstr($html, '[/i]')) {
            $tags = process_tags(match_tags($html, '\[i\]|\[\/i\]'));
            foreach ($tags as $k => $v) $html = simple_replace($html, $k, $v);
        }

        if (strstr($html, '[/u]')) {
            $tags = process_tags(match_tags($html, '\[u\]|\[\/u\]'));
            foreach ($tags as $k => $v) $html = simple_replace($html, $k, $v);
        }

        if (strstr($html, '[/s]') || strstr($html, '[/sup]') || strstr($html, '[/sub]')) {
            $tags = process_tags(match_tags($html, '\[s\]|\[\/s\]'));
            foreach ($tags as $k => $v) $html = simple_replace($html, $k, $v);

            $tags = process_tags(match_tags($html, '\[sup\]|\[\/sup\]'));
            foreach ($tags as $k => $v) $html = simple_replace($html, $k, $v);

            $tags = process_tags(match_tags($html, '\[sub\]|\[\/sub\]'));
            foreach ($tags as $k => $v) $html = simple_replace($html, $k, $v);
        }

        if (strstr($html, '[/size]')) {
            $tags = process_tags(match_tags($html, '\[size\s*=\s*([^\\"\'><]+?)\s*\]|\[\/size\]'));
            foreach ($tags as $k => $v) {
                if ($v == '[/size]') {
                    $html = substr_replace($html, '</span>', $k, mb_strlen($v));
                } else {
                    $replace = preg_replace_callback('/\[size\s*=\s*(.+?)\s*\]/i', 'getSizeName', $v);
                    $html = substr_replace($html, $replace, $k, mb_strlen($v));
                }
            }
        }

        if (strstr($html, '[/color]')) {
            $tags = process_tags(match_tags($html, '\[color\s*=\s*([^\\"\'><]+?)\s*\]|\[\/color\]'));
            foreach ($tags as $k => $v) {
                if ($v == '[/color]') {
                    $html = substr_replace($html, '</span>', $k, mb_strlen($v));
                } else {
                    $replace = preg_replace('/\[color\s*=\s*(.+?)\s*\]/i', '<span style="color:$1;">', $v);
                    $html = substr_replace($html, $replace, $k, mb_strlen($v));
                }
            }
        }

        if (strstr($html, '[/font]')) {
            $tags = process_tags(match_tags($html, '\[font\s*=\s*([^\\"\'><]+?)\s*\]|\[\/font\]'));
            foreach ($tags as $k => $v) {
                if ($v == '[/font]') {
                    $html = substr_replace($html, '</span>', $k, mb_strlen($v));
                } else {
                    $replace = preg_replace('/\[font\s*=\s*(.+?)\s*\]/i', '<span style="font-family:$1;">', $v);
                    $html = substr_replace($html, $replace, $k, mb_strlen($v));
                }
            }
        }

        if (strstr($html, '[/back]')) {
            $tags = process_tags(match_tags($html, '\[back\s*=\s*([^\\"\'><]+?)\s*\]|\[\/back\]'));
            foreach ($tags as $k => $v) {
                if ($v == '[/back]') {
                    $html = substr_replace($html, '</span>', $k, mb_strlen($v));
                } else {
                    $replace = preg_replace('/\[back\s*=\s*(.+?)\s*\]/i', '<span style="background-color:$1;">', $v);
                    $html = substr_replace($html, $replace, $k, mb_strlen($v));
                }
            }
        }

        $quoteMatch = array();
        $html = beforeQuote($html);

        if (strstr($html, '[/align]')) {
            $tags = process_tags(match_tags($html, '\[align\s*=\s*([^\\"\'><]+?)\s*\]|\[\/align\]'));
            foreach ($tags as $k => $v) {
                if ($v == '[/align]') {
                    $html = substr_replace($html, '</p>', $k, mb_strlen($v));
                } else {
                    $replace = preg_replace('/\[align\s*=\s*(.+?)\s*\]/i', '<p align="$1">', $v);
                    $html = substr_replace($html, $replace, $k, mb_strlen($v));
                }
            }
        }

        if (strstr($html, '[/img]')) {
            $html = preg_replace('/\[img\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/img\]/i', '<img src="$1" border="0" alt="' . $title . '" onload="if(this.width>700){this.width=700;}"/>', $html);
            $html = preg_replace('/\[img=(.*?)\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/img\]/i', '<img src="$2" border="0" alt="' . $title . '" onload="if(this.width>700){this.width=700;}"/>', $html);
            $html = preg_replace_callback('/\[img\s*=(?:\s*(\d*%?)\s*,\s*(\d*%?)\s*)?(?:,?\s*(\w+))?\s*\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/img\]/i', 'getImg', $html);
        }

        if (strstr($html, '[/url]')) {
            $html = preg_replace_callback('/\[url\]\s*((?!")[\s\S]*?)(?:"[\s\S]*?)?\s*\[\/url\]/i', 'getUrl', $html);
            $html = preg_replace_callback('/\[url\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]*?)\s*\[\/url\]/i', 'getUrl', $html);
        }

        if (strstr($html, '[/email]')) {
            $html = preg_replace('/\[email\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/email\]/i', '<a href="mailto:$1">$1</a>', $html);
            $html = preg_replace('/\[email\s*=\s*([^\]"]+?)(?:"[^\]]*?)?\s*\]\s*([\s\S]+?)\s*\[\/email\]/i', '<a href="mailto:$1">$2</a>', $html);
        }

        if (strstr($html, '[/flash]')) {
            $html = preg_replace_callback('/\[flash\s*(?:=\s*(\d+)\s*,\s*(\d+)\s*)?\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/flash\]/i', 'getFlash', $html);
        }

        if (strstr($html, '[/media]')) {
            $html = preg_replace_callback('/\[media\s*(?:=\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*(\d+)\s*)?)?\]\s*(((?!")[\s\S])+?)(?:"[\s\S]*?)?\s*\[\/media\]/i', 'getMedia', $html);
        }

        if (strstr($html, '[/table]') || strstr($html, '[/tr]') || strstr($html, '[/td]')) {
            $html = preg_replace_callback('/\[table\s*(?:=(\d{1,4}%?)\s*(?:,\s*([^\]"]+)(?:"[^\]]*?)?)?)?\s*\]/i', 'getTable', $html);
            $html = preg_replace_callback('/\[tr\s*(?:=(\s*[^\]"]+))?(?:"[^\]]*?)?\s*\]/i', 'getTR', $html);
            $html = preg_replace_callback("/\[td\s*(?:=\s*(\d{1,2})\s*,\s*(\d{1,2})\s*(?:,\s*(\d{1,4}%?))?)?\s*\]/i", 'getTD', $html);
            $html = preg_replace("/\[\/(table|tr|td)\]/i", '</$1>', $html);
        }
        $html = preg_replace("/\[\*\]([^\[]+)/i", '<li>$1</li>', $html);

        if (strstr($html, '[/list]')) {
            $html = preg_replace_callback('/\[list\s*(?:=\s*([^\]"]+))?(?:"[^\]]*?)?\s*\]/i', 'getUL', $html);
            $html = preg_replace("/\[\/list\]/i", '</ul>', $html);
        }
        $html = preg_replace_callback('/(^|<\/?\w+(?:\s+[^>]*?)?>)([^<$]+)/i', 'fixText', $html);

        $html = afterQuote($html);

        $ajk_smilies_code = array('adfaferyikuhjd', 'mclmlhha', 'bsnlghsha', 'utyrgfdhtrueyrytr', 'y6u87ityhdfgre56ruy', 'dafjlajgljdg', '5ythdfgsdvssgretrjgdf', 'verhetrygnbvxfbcnv', 'ggfhgfhjgfdfhgsrjfgnb', 'gfhyjrutyhgfdhtryetrht', 'bavdafretry65ui6i', 'iowtjowqnhgs', 'dfewryhwtyy', 'ruyerytyurttutiy', 'bzbgahah', 'hglajgaljhhalh', 'ghdsjsksk', 'lajldjfagj', 'faljsdfjaljgsd', 'lajfldajga', 'nlanlnlv023', 'wohdnhlasdh', 'zanglahnal', 'adyyjuyjkukuki', 'cvsfg5tryet6hu6yejy', 'fetrutiyooopop', 'hgfhrgjtrhy6u565656', 'dfadfertyry');
        $ajk_simlies_url = array('<img src="http://forum.anjuke.com/images/smilies/Onion_30.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_06.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_24.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_58.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_60.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_46.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_01.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_49.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_51.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_53.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_72.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_10.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_17.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_68.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_22.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_38.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_07.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_40.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_23.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_59.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_70.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_14.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_19.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_64.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_39.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_69.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_02.gif">', '<img src="http://forum.anjuke.com/images/smilies/Onion_09.gif">');
        $html = str_replace($ajk_smilies_code, $ajk_simlies_url, $html);


        $html = preg_replace("/\[(\/?)(b|u|i|s|sup|sub)\]/i", '', $html);
        $html = preg_replace("/\[size\s*=\s*(.+?)\s*\]/i", '', $html);
        $html = preg_replace('/\[font\s*=\s*(.+?)\s*\]/i', '', $html);
        $html = preg_replace('/\[back\s*=\s*(.+?)\s*\]/i', '', $html);
        $html = preg_replace('/\[color\s*=\s*(.+?)\s*\]/i', '', $html);
        $html = preg_replace("/\[\/(color|size|font|back)\]/i", '', $html);

        $bUbb2htmlFunctionInit = true;
        return $html;
    }
}