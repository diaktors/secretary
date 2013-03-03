<?php
namespace Secretery\View\Helper;

use dflydev\markdown\MarkdownExtraParser;
use Zend\View\Helper\AbstractHelper;

class Markdown extends AbstractHelper
{
    /**
     * @param $string
     */
    public function markdown($string)
    {
        $markdownParser = new MarkdownExtraParser();
        echo $markdownParser->transformMarkdown($string);
    }

    /**
     * @param  null $string
     * @return string
     */
    public function __invoke($string = null)
    {
        return $this->markdown($string);
    }
}