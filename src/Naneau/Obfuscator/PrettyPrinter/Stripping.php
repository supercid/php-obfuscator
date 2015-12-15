<?php
/**
 * Stripping.php
 *
 * @package         Obfuscator
 * @subpackage      Obfuscator
 */

namespace Naneau\Obfuscator\PrettyPrinter;

/**
 * Stripping
 *
 * A pretty printer that strips all newlines, but preserves indents where
 * needed.
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      Obfuscator
 */
class Stripping extends \PhpParser\PrettyPrinter\Standard
{
    protected function pStmts(array $nodes, $indent = true)
    {
        $result = parent::pStmts($nodes, false);

        return preg_replace('~\n(?!$|' . $this->noIndentToken . ')~', "", $result);
    }

    protected function pComments(array $comments)
    {
        return $this->pNoIndent(trim(parent::pComments($comments)));
    }
}
