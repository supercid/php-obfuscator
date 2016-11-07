<?php
/**
 * RemoveComments.php
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */

namespace Naneau\Obfuscator\Node\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Comment;

use \InvalidArgumentException;

/**
 * RemoveComments
 *
 * Removes comments, optionally preserving annotations.
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
class RemoveComments extends NodeVisitorAbstract
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
        // doc comment blocks are processed.
        $comments = [];

        if ($this->preserveAnnotations) {
            $docComment = $node->getDocComment();

            if ($docComment) {
                $text = $docComment->getText();

                // Check if it is a doc comment.
                if (strpos($text, "/**") !== false) {
                    $text = $this->stripComment($text);

                    if ($text) {
                        $comments = [ new Comment($text) ];
                    }
                }
            }
        }

        // Remove (or set) comments.
        $node->setAttribute("comments", $comments);

        return $node;
    }

    /**
     * Process a doc comment block.
     *
     * @param string $text
     * @return string
     */
    private function stripComment($text)
    {
        if ($this->annotationsFlavor == "generic" || $this->annotationsFlavor == "generic-newline") {
            $start = strpos($text, "/**") + strlen("/**");
            $stop = strpos($text, "*/");
            $text = trim(substr($text, $start, $stop - $start));

            // Iterate over each line and stop removing lines until the first
            // annotation is encountered.
            $lines = explode("\n", $text);

            while ($lines) {
                if (preg_match("/\\s*\\*\\s*@/", $lines[0])) {
                    break;
                }

                array_shift($lines);
            }

            // Strip the * character from the lines when using generic. Without
            // stripping this character, some annotations could become invalid.
            if ($this->annotationsFlavor == "generic") {
                $lines = array_map(function ($line) {
                    $index = strpos($line, "*");

                    if ($index !== false) {
                        return trim(substr($line, $index + 1));
                    } else {
                        return $line;
                    }
                }, $lines);
            }

            // Re-join the lines
            if ($lines) {
                if ($this->annotationsFlavor == "generic") {
                    return "/** " . implode(" ", $lines) . " */";
                } elseif ($this->annotationsFlavor == "generic-newline") {
                    return "/**\n" . implode("\n", $lines) . "\n */";
                }
            }
        }
    }

    /**
     * Set preserve annotations in docblocks.
     *
     * @param bool $preserveAnnotations
     * @return ScrambleComment
     */
    public function setPreserveAnnotations($preserveAnnotations)
    {
        $this->preserveAnnotations = $preserveAnnotations;

        return $this;
    }

    /**
     * Set annotations flavor for preservation.
     *
     * Supported flavors:
     * - generic: Text + annotations (using at-sign). Text is removed and all
     *            newlines are removed. It is assumed that multiple annotations
     *            can be specified on the same line.
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
