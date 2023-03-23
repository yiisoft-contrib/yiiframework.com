<?php

namespace app\apidoc;

use CHtmlPurifier;
use HTMLPurifier_Bootstrap;
use yii\helpers\Html;

require_once(dirname(__DIR__) . '/data/yii-1.1/framework/vendors/markdown/markdown.php');
if(!class_exists('HTMLPurifier_Bootstrap',false))
{
	require_once(dirname(__DIR__) . '/data/yii-1.1/framework/vendors/htmlpurifier/HTMLPurifier.standalone.php');
	HTMLPurifier_Bootstrap::registerAutoload();
}


class Yii1MarkdownParser extends \MarkdownExtra_Parser
{
	private $_blockquoteType='';

    public $headerCount = 1;

	public function __construct()
	{
		$this->span_gamut += array(
			"doApiLinks"        => 35,
			);

		parent::__construct();
	}

	public function safeTransform($content)
	{
		$content=$this->transform($content);
		$purifier=new CHtmlPurifier;
		return $purifier->purify($content);
	}

    /**
     * Overrides parent, rtrim code first
     */
	public function _doCodeBlocks_callback($matches)
    {
		$codeblock = rtrim($this->outdent($matches[1]));
		if(($codeblock = $this->highlightCodeBlock($codeblock)) !== null)
			return "\n\n".$this->hashBlock($codeblock)."\n\n";
		else
			return \MarkdownExtra_Parser::_doCodeBlocks_callback($matches);
	}

	public function _doHeaders_callback_setext($matches)
	{
		if ($matches[3] == '-' && preg_match('{^- }', $matches[1]))
			return $matches[0];
		$level = $matches[3]{0} == '=' ? 1 : 2;
		$text = $this->runSpanGamut($matches[1]);
		$attr = $this->doHeaderId($text);
		$block = "<h$level$attr>".$text."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}

	public function _doHeaders_callback_atx($matches)
	{
		$level = strlen($matches[1]);
		$text = $this->runSpanGamut($matches[2]);
		$attr = $this->doHeaderId($text);
		$block = "<h$level$attr>".$text."</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}

	public function _doBlockQuotes_callback($matches)
	{
		$bq = $matches[1];
		# trim one level of quoting - trim whitespace-only lines
		$bq = preg_replace('/^[ ]*>[ ]?|^[ ]+$/m', '', $bq);
		$bq = $this->runBlockGamut($bq);		# recurse

		$bq = preg_replace('/^/m', "  ", $bq);
		# These leading spaces cause problem with <pre> content,
		# so we need to fix that:
		$bq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx',
			array(&$this, '_DoBlockQuotes_callback2'), $bq);

		# Do blockquote tips/notes
		$bq = preg_replace_callback('/^(\s*<p>\s*)([^:]+):\s*/sxi',
			array($this, 'doBlockquoteTypes'), $bq);
		$attr= $this->_blockquoteType ? " class=\"{$this->_blockquoteType}\"" : '';
		return "\n". $this->hashBlock("<blockquote{$attr}>\n$bq\n</blockquote>")."\n\n";
	}

	public function doHeaderId($text)
	{
		$id = preg_replace('/[^a-z0-9]/', '-', strtolower($text));
        $id = str_replace('/-{2,}/', '-', $id);
        if(preg_match('/--/', $id))
            $id = 'sec-'.$this->headerCount++;
		return " id=\"$id\"";
	}

	public function doBlockquoteTypes($matches)
	{
		if(($pos=strpos($matches[2],'|'))!==false)
		{
			$type_str=substr($matches[2],$pos+1);
			$this->_blockquoteType=strtolower(substr($matches[2],0,$pos));
		}
		else
		{
			$this->_blockquoteType = strtolower($matches[2]);
			$type_str= ucwords($this->_blockquoteType);
		}
		return "<p><strong>$type_str:</strong> ";
	}

	public function doApiLinks($text)
	{
		return preg_replace_callback('/(?<!\])\[([^\]]+)\](?!\[)/', array($this, 'formatApiLinks'), $text);
	}

	public function formatApiLinks($match)
	{
		@list($text, $api) = explode('|', $match[1], 2);
		$api= $api===null ? $text: $api;
		if(
			strncmp($api,'C',1)!==0 &&
			strncmp($api,'Yii',3)!==0 &&
			strncmp($api,'Gii',3)!==0 &&
			strncmp($api,'I',1)!==0
		)
			return $match[0];
		$segs=explode('::',rtrim(rtrim($api,'()')));
		$class=$segs[0];
		$anchor=isset($segs[1]) ? '#'.$segs[1] : '';
		$url = '/doc/api/'.$class.$anchor;
		$link = "<a href=\"$url\">$text</a>";
		return $this->hashPart($link);
	}

    /**
   	 * Generates the config for the highlighter.
   	 * @param string $options user-entered options
   	 * @return array the highlighter config
   	 */
   	public function getHighlightConfig($options)
   	{
   		$config = array('use_language'=>false);
   		if( $this->getInlineOption('showLineNumbers', $options, false) )
   			$config['numbers'] = HL_NUMBERS_LI;
   		$config['tabsize'] = $this->getInlineOption('tabSize', $options, 4);
   		return $config;
   	}

    /**
   	 * @var string the css class for the div element containing
   	 * the code block that is highlighted. Defaults to 'hl-code'.
   	 */
   	public $highlightCssClass='hl-code';
   	/**
   	 * @var mixed the options to be passed to {@link https://htmlpurifier.org HTML Purifier}.
   	 * This can be a HTMLPurifier_Config object,  an array of directives (Namespace.Directive => Value)
   	 * or the filename of an ini file.
   	 * This property is used only when {@link safeTransform} is invoked.
   	 * @see https://htmlpurifier.org/live/configdoc/plain.html
   	 * @since 1.1.4
   	 */
   	public $purifierOptions=null;


   	/**
   	 * @return string the default CSS file that is used to highlight code blocks.
   	 */
   	public function getDefaultCssFile()
   	{
   		return dirname(__DIR__) . '/data/yii-1.1/framework/vendors/TextHighlighter/highlight.css';
   	}

   	/**
   	 * Callback function when a fenced code block is matched.
   	 * @param array $matches matches
   	 * @return string the highlighted code block
   	 */
   	public function _doFencedCodeBlocks_callback($matches)
   	{
   		return "\n\n".$this->hashBlock($this->highlightCodeBlock($matches[2]))."\n\n";
   	}

   	/**
   	 * Highlights the code block.
   	 * @param string $codeblock the code block
   	 * @return string the highlighted code block. Null if the code block does not need to highlighted
   	 */
   	protected function highlightCodeBlock($codeblock)
   	{
   		if(($tag=$this->getHighlightTag($codeblock))!==null && ($highlighter=$this->createHighLighter($tag)))
   		{
   			$codeblock = preg_replace('/\A\n+|\n+\z/', '', $codeblock);
   			$tagLen = strpos($codeblock, $tag)+strlen($tag);
   			$codeblock = ltrim(substr($codeblock, $tagLen));
   			$output=preg_replace('/<span\s+[^>]*>(\s*)<\/span>/', '\1', $highlighter->highlight($codeblock));
   			return "<div class=\"{$this->highlightCssClass}\">".$output."</div>";
   		}
   		else
   			return "<pre>".Html::encode($codeblock)."</pre>";
   	}

   	/**
   	 * Returns the user-entered highlighting options.
   	 * @param string $codeblock code block with highlighting options.
   	 * @return string the user-entered highlighting options. Null if no option is entered.
   	 */
   	protected function getHighlightTag($codeblock)
   	{
   		$str = trim(current(preg_split("/\r|\n/", $codeblock,2)));
   		if(strlen($str) > 2 && $str[0] === '[' && $str[strlen($str)-1] === ']')
   			return $str;
   	}

   	/**
   	 * Creates a highlighter instance.
   	 * @param string $options the user-entered options
   	 * @return \Text_Highlighter the highlighter instance
   	 */
   	protected function createHighLighter($options)
   	{
   		if(!class_exists('Text_Highlighter', false))
   		{
   			require_once(dirname(__DIR__) . '/data/yii-1.1/framework/vendors/TextHighlighter/Text/Highlighter.php');
   			require_once(dirname(__DIR__) . '/data/yii-1.1/framework/vendors/TextHighlighter/Text/Highlighter/Renderer/Html.php');
   		}
   		$lang = current(preg_split('/\s+/', substr(substr($options,1), 0,-1),2));
   		$highlighter = \Text_Highlighter::factory($lang);
   		if($highlighter)
   			$highlighter->setRenderer(new \Text_Highlighter_Renderer_Html($this->getHighlightConfig($options)));
   		return $highlighter;
   	}

   	/**
   	 * Retrieves the specified configuration.
   	 * @param string $name the configuration name
   	 * @param string $str the user-entered options
   	 * @param mixed $defaultValue default value if the configuration is not present
   	 * @return mixed the configuration value
   	 */
   	protected function getInlineOption($name, $str, $defaultValue)
   	{
   		if(preg_match('/'.$name.'(\s*=\s*(\d+))?/i', $str, $v) && count($v) > 2)
   			return $v[2];
   		else
   			return $defaultValue;
   	}
}
