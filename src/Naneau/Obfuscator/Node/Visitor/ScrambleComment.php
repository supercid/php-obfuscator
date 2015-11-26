<?php
/**
 * ScrambleComment.php
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */

namespace Naneau\Obfuscator\Node\Visitor;

use Naneau\Obfuscator\Node\Visitor\Scrambler as ScramblerVisitor;
use Naneau\Obfuscator\StringScrambler;

use PhpParser\Node;
use PhpParser\Comment;

use \InvalidArgumentException;

/**
 * ScrambleComment
 *
 * Renames parameters
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
class ScrambleComment extends ScramblerVisitor
{
    /**
     * Preserve annotations?
     *
     * @var bool
     **/
    private $preserveAnnotations = false;

    /**
     * Annotations flavor.
     *
     * @var string
     **/
    private $annotationsFlavor = "generic";

    /**
     * Check all nodes
     *
     * @param  Node $node
     * @return void
     **/
    public function enterNode(Node $node)
    {
        // Skip nodes without comments.
        if (!$node->hasAttribute("comments")) {
            return;
        }

        // Check if annotations should be preserved. Only nodes with actual
        // doccomment blocks are processed.
        $comments = [];

        if ($this->preserveAnnotations) {
            $docComment = $node->getDocComment();

            if ($docComment) {
                $text = $docComment->getText();

                // Verify that it is a real comment.
                if (strpos($text, "/**") !== false) {
                    $comments = [ new Comment($this->stripComment($text)) ];
                }
            }
        }

        // Remove (or set) comments.
        $node->setAttribute("comments", $comments);

        return $node;
    }

    /**
     * Process a doccomment block.
     *
     * @param string $text
     * @return string
     */
    private function stripComment($text)
    {
        if ($this->annotationsFlavor == "generic" ||
            $this->annotationsFlavor == "generic-newline") {

            $start = strpos($text, "/**") + strlen("/**");
            $stop = strpos($text, "*/");
            $text = trim(substr($text, $start, $stop - $start));

            // Iterate over eacht line and stop removing lines until the first
            // annotation is encountered.
            $lines = explode("\n", $text);

            while ($lines) {
                if (preg_match("/\\s*\\*\\s*@/", $lines[0])) {
                    break;
                }

                array_shift($lines);
            }

            // Re-join the lines
            if ($this->annotationsFlavor == "generic") {
                return "/** " . implode(" ", $lines) . " */";
            } elseif ($this->annotationsFlavor == "generic-newline") {
                return "/**\n" . implode("\n", $lines) . "\n */";
            }
        }
    }

    /**
     * Set preserve annotations in docblocks.
     *
     * @param bool $preserveAnnotations
     * @return void
     */
    public function setPreserveAnnotations($preserveAnnotations)
    {
        $this->preserveAnnotations = $preserveAnnotations;
    }

    /**
     * Set annotations flavor for preservation.
     *
     * Supported flavors:
     * - generic: Text + annotations (using at-sign). Text is removed and all
     *            newlines are removed.
     * - generic-newline: Same as generic, but preserves newlines.
     *
     * @param string $annotationsFlavor
     * @return void
     */
    public function setAnnotationsFlavor($annotationsFlavor)
    {
        $supported = ["generic", "generic-newline"];

        if (!in_array($annotationsFlavor, $supported)) {
            throw new InvalidArgumentException(
                "Unsupported annontations flavor: {$this->annotationsFlavor}"
            );
        }

        $this->annotationsFlavor = $annotationsFlavor;
    }
}
