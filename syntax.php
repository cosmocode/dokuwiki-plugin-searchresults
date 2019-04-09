<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Todd Augsburger <todd@rollerorgans.com>
 */

class syntax_plugin_searchresults extends DokuWiki_Syntax_Plugin
{

    function getType()
    {
        return 'substition';
    }

    function getSort()
    {
        return 300;
    }

    function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern("{{search>.*?}}", $mode, 'plugin_searchresults');
    }

    function handle($match, $state, $pos, Doku_Handler $handler)
    {
        if ($state == DOKU_LEXER_SPECIAL) {
            // strip / from start and / from end
            $match = substr($match, 9, -2);
            return array($state, $match);
        }
        return array();
    }

    //natsort an array of pagenames
    function _addSorted(&$target, $names)
    {
        global $conf;
        if ($conf['useheading']) {
            // sort by headings
            $title_array = array();
            foreach ($names as $key => $value) {
                if ($title = p_get_first_heading($value))
                    $title_array[$key] = $title;
                else
                    $title_array[$key] = $value;
            }
            natsort($title_array);
            foreach ($title_array as $key => $value)
                $target[] = $names[$key];
        } else {
            // sort by pagenames
            natsort($names);
            foreach ($names as $value)
                $target[] = $value;
        }
    }

    function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode == 'xhtml') {
            list($state, $match) = $data;
            if ($state == DOKU_LEXER_SPECIAL) {
                $search = ft_pageSearch($match, $poswords);
                if (count($search)) {
                    $renderer->doc .= "<ul>\n";
                    $key_array = array();
                    $this->_addSorted($key_array, array_keys($search));
                    foreach ($key_array as $value) {
                        $renderer->doc .= '<li class="level1"><div class="li">';
                        $renderer->doc .= html_wikilink(':' . $value);
                        $renderer->doc .= "</div>\n";
                    }
                    $renderer->doc .= "</ul>\n";
                }
            }
            return true;
        }
        return false;
    }
}
