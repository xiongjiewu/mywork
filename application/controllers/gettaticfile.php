<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 网站css/js压缩控制class
 * added by xiongjiewu at 2013-3-4
 */
class Gettaticfile extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->css();
    }

    /**
     * css压缩
     */
    public function css() {
        $path = $this->input->get("path");
        if (empty($path)) {
            echo "";
            exit;
        }
        $path = base64_decode($path);
        $pathArr = explode(";",$path);
        $pathArr = array_filter($pathArr);
        if (empty($pathArr)) {
            echo "";
            exit;
        }

        //输出Javascript
        header('Content-type: text/css');
        $this->_zipCssFileText($pathArr,'Content-type: text/css');
    }

    /**
     * 压缩js文件
     */
    public function js() {
        $path = $this->input->get("path");
        if (empty($path)) {
            echo "";
            exit;
        }
        $path = base64_decode($path);
        $pathArr = explode(";",$path);
        $pathArr = array_filter($pathArr);
        if (empty($pathArr)) {
            echo "";
            exit;
        }
        $this->_zipJsFileText($pathArr,'Content-type: text/javascript');
    }

    /**
     * 压缩css文件
     * @param $pathArr
     * @param $header
     */
    private function _zipCssFileText($pathArr,$header) {
        //输出文件内容
        header($header);
        $fileText = "";
        foreach($pathArr as $path) {
            $fileText .= file_get_contents("." . $path);
        }

        //压缩开始
        $fileText = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $fileText);
        /* remove tabs, spaces, newlines, etc. */
        $fileText = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $fileText);
        echo $fileText;
    }

    /**
     * 压缩js文件
     * @param $pathArr
     * @param $header
     */
    private function _zipJsFileText($pathArr,$header) {
        //输出文件内容
        header($header);
        $fileText = "";
        foreach($pathArr as $path) {
            $fileText .= file_get_contents("." . $path);
        }

        //压缩开始
        $fileText = JSMin::minify($fileText);
        echo $fileText;
    }
}

class JSMin {
    const ORD_LF            = 10;
    const ORD_SPACE         = 32;
    const ACTION_KEEP_A     = 1;
    const ACTION_DELETE_A   = 2;
    const ACTION_DELETE_A_B = 3;

    protected $a           = "\n";
    protected $b           = '';
    protected $input       = '';
    protected $inputIndex  = 0;
    protected $inputLength = 0;
    protected $lookAhead   = null;
    protected $output      = '';

    /**
     * Minify Javascript
     *
     * @param string $js Javascript to be minified
     * @return string
     */
    public static function minify($js)
    {
        // look out for syntax like "++ +" and "- ++"
        $p = '\\+';
        $m = '\\-';
        if (preg_match("/([$p$m])(?:\\1 [$p$m]| (?:$p$p|$m$m))/", $js)) {
            // likely pre-minified and would be broken by JSMin
            return $js;
        }
        $jsmin = new JSMin($js);
        return $jsmin->min();
    }

    /*
     * Don't create a JSMin instance, instead use the static function minify,
     * which checks for mb_string function overloading and avoids errors
     * trying to re-minify the output of Closure Compiler
     *
     * @private
     */
    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Perform minification, return result
     */
    public function min()
    {
        if ($this->output !== '') { // min already run
            return $this->output;
        }

        $mbIntEnc = null;
        if (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2)) {
            $mbIntEnc = mb_internal_encoding();
            mb_internal_encoding('8bit');
        }
        $this->input = str_replace("\r\n", "\n", $this->input);
        $this->inputLength = strlen($this->input);

        $this->action(self::ACTION_DELETE_A_B);

        while ($this->a !== null) {
            // determine next command
            $command = self::ACTION_KEEP_A; // default
            if ($this->a === ' ') {
                if (! $this->isAlphaNum($this->b)) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif ($this->a === "\n") {
                if ($this->b === ' ') {
                    $command = self::ACTION_DELETE_A_B;
                    // in case of mbstring.func_overload & 2, must check for null b,
                    // otherwise mb_strpos will give WARNING
                } elseif ($this->b === null
                    || (false === strpos('{[(+-', $this->b)
                        && ! $this->isAlphaNum($this->b))) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif (! $this->isAlphaNum($this->a)) {
                if ($this->b === ' '
                    || ($this->b === "\n"
                        && (false === strpos('}])+-"\'', $this->a)))) {
                    $command = self::ACTION_DELETE_A_B;
                }
            }
            $this->action($command);
        }
        $this->output = trim($this->output);

        if ($mbIntEnc !== null) {
            mb_internal_encoding($mbIntEnc);
        }
        return $this->output;
    }

    /**
     * ACTION_KEEP_A = Output A. Copy B to A. Get the next B.
     * ACTION_DELETE_A = Copy B to A. Get the next B.
     * ACTION_DELETE_A_B = Get the next B.
     */
    protected function action($command)
    {
        switch ($command) {
            case self::ACTION_KEEP_A:
                $this->output .= $this->a;
            // fallthrough
            case self::ACTION_DELETE_A:
                $this->a = $this->b;
                if ($this->a === "'" || $this->a === '"') { // string literal
                    $str = $this->a; // in case needed for exception
                    while (true) {
                        $this->output .= $this->a;
                        $this->a       = $this->get();
                        if ($this->a === $this->b) { // end quote
                            break;
                        }
                        if (ord($this->a) <= self::ORD_LF) {
                            throw new JSMin_UnterminatedStringException(
                                "JSMin: Unterminated String at byte "
                                    . $this->inputIndex . ": {$str}");
                        }
                        $str .= $this->a;
                        if ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a       = $this->get();
                            $str .= $this->a;
                        }
                    }
                }
            // fallthrough
            case self::ACTION_DELETE_A_B:
                $this->b = $this->next();
                if ($this->b === '/' && $this->isRegexpLiteral()) { // RegExp literal
                    $this->output .= $this->a . $this->b;
                    $pattern = '/'; // in case needed for exception
                    while (true) {
                        $this->a = $this->get();
                        $pattern .= $this->a;
                        if ($this->a === '/') { // end pattern
                            break; // while (true)
                        } elseif ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a       = $this->get();
                            $pattern      .= $this->a;
                        } elseif (ord($this->a) <= self::ORD_LF) {
                            throw new JSMin_UnterminatedRegExpException(
                                "JSMin: Unterminated RegExp at byte "
                                    . $this->inputIndex .": {$pattern}");
                        }
                        $this->output .= $this->a;
                    }
                    $this->b = $this->next();
                }
            // end case ACTION_DELETE_A_B
        }
    }

    protected function isRegexpLiteral()
    {
        if (false !== strpos("\n{;(,=:[!&|?", $this->a)) { // we aren't dividing
            return true;
        }
        if (' ' === $this->a) {
            $length = strlen($this->output);
            if ($length < 2) { // weird edge case
                return true;
            }
            // you can't divide a keyword
            if (preg_match('/(?:case|else|in|return|typeof)$/', $this->output, $m)) {
                if ($this->output === $m[0]) { // odd but could happen
                    return true;
                }
                // make sure it's a keyword, not end of an identifier
                $charBeforeKeyword = substr($this->output, $length - strlen($m[0]) - 1, 1);
                if (! $this->isAlphaNum($charBeforeKeyword)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get next char. Convert ctrl char to space.
     */
    protected function get()
    {
        $c = $this->lookAhead;
        $this->lookAhead = null;
        if ($c === null) {
            if ($this->inputIndex < $this->inputLength) {
                $c = $this->input[$this->inputIndex];
                $this->inputIndex += 1;
            } else {
                return null;
            }
        }
        if ($c === "\r" || $c === "\n") {
            return "\n";
        }
        if (ord($c) < self::ORD_SPACE) { // control char
            return ' ';
        }
        return $c;
    }

    /**
     * Get next char. If is ctrl character, translate to a space or newline.
     */
    protected function peek()
    {
        $this->lookAhead = $this->get();
        return $this->lookAhead;
    }

    /**
     * Is $c a letter, digit, underscore, dollar sign, escape, or non-ASCII?
     */
    protected function isAlphaNum($c)
    {
        return (preg_match('/^[0-9a-zA-Z_\\$\\\\]$/', $c) || ord($c) > 126);
    }

    protected function singleLineComment()
    {
        $comment = '';
        while (true) {
            $get = $this->get();
            $comment .= $get;
            if (ord($get) <= self::ORD_LF) { // EOL reached
                // if IE conditional comment
                if (preg_match('/^\\/@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                    return "/{$comment}";
                }
                return $get;
            }
        }
    }

    protected function multipleLineComment()
    {
        $this->get();
        $comment = '';
        while (true) {
            $get = $this->get();
            if ($get === '*') {
                if ($this->peek() === '/') { // end of comment reached
                    $this->get();
                    // if comment preserved by YUI Compressor
                    if (0 === strpos($comment, '!')) {
                        return "\n/*" . substr($comment, 1) . "*/\n";
                    }
                    // if IE conditional comment
                    if (preg_match('/^@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                        return "/*{$comment}*/";
                    }
                    return ' ';
                }
            } elseif ($get === null) {
                throw new JSMin_UnterminatedCommentException(
                    "JSMin: Unterminated comment at byte "
                        . $this->inputIndex . ": /*{$comment}");
            }
            $comment .= $get;
        }
    }

    /**
     * Get the next character, skipping over comments.
     * Some comments may be preserved.
     */
    protected function next()
    {
        $get = $this->get();
        if ($get !== '/') {
            return $get;
        }
        switch ($this->peek()) {
            case '/': return $this->singleLineComment();
            case '*': return $this->multipleLineComment();
            default: return $get;
        }
    }
}

class JSMin_UnterminatedStringException extends Exception {}
class JSMin_UnterminatedCommentException extends Exception {}
class JSMin_UnterminatedRegExpException extends Exception {}