<?php
/**
 * ScramblePrivateProperty.php
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */

namespace Naneau\Obfuscator\Node\Visitor;

use Naneau\Obfuscator\Node\Visitor\TrackingRenamerTrait;
use Naneau\Obfuscator\Node\Visitor\SkipTrait;

use Naneau\Obfuscator\Node\Visitor\Scrambler as ScramblerVisitor;
use Naneau\Obfuscator\StringScrambler;

use PhpParser\Node;

use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Property;

use PhpParser\Node\Expr\PropertyFetch;

use PhpParser\Node\Expr\Variable;

/**
 * ScramblePrivateProperty
 *
 * Renames private properties
 *
 * WARNING
 *
 * See warning for private method scrambler
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
class ScramblePrivateProperty extends ScramblerVisitor
{
    use SkipTrait;
    use TrackingRenamerTrait;

    /**
     * Constructor
     *
     * @param  StringScrambler $scrambler
     * @return void
     **/
    public function __construct(StringScrambler $scrambler)
    {
        parent::__construct($scrambler);
    }

    /**
     * Before node traversal
     *
     * @param  Node[] $nodes
     * @return array
     **/
    public function beforeTraverse(array $nodes)
    {
        $this
            ->resetRenamed()
            ->scanPropertyDefinitions($nodes);

        return $nodes;
    }

    /**
     * Check all variable nodes
     *
     * @param  Node $node
     * @return void
     **/
    public function enterNode(Node $node)
    {
        if ($node instanceof PropertyFetch) {

            if (!is_string($node->name)) {
                return;
            }

            // Method call is not calling private methods
            if (!$this->isPrivateProperty($node)) {
                return;
            }

            if ($this->isRenamed($node->name)) {
                $node->name = $this->getNewName($node->name);
                return $node;
            }
        }
    }

    /**
     * Check if a given variable is a private property.
     *
     * @param  Property $node
     * @return bool
     **/
    private function isPrivateProperty(PropertyFetch $node)
    {
        $meta = $node->meta;

        // It's never a private property outside a class.
        if (!$meta->class) {
            return false;
        }

        // $this always points to current instance.
        if ($node->var instanceof Variable && $node->var->name === "this") {
            return true;
        }

        // Not sure what type of object is, so return false.
        return false;
    }

    /**
     * Recursively scan for private method definitions and rename them
     *
     * @param  Node[] $nodes
     * @return void
     **/
    private function scanPropertyDefinitions(array $nodes)
    {
        foreach ($nodes as $node) {
            // Scramble the private method definitions
            if ($node instanceof Property && ($node->type & ClassNode::MODIFIER_PRIVATE)) {
                foreach($node->props as $property) {

                    // Record original name and scramble it
                    $originalName = $property->name;
                    $this->scramble($property);

                    // Record renaming
                    $this->renamed($originalName, $property->name);
                }

            }

            // Recurse over child nodes
            if (isset($node->stmts) && is_array($node->stmts)) {
                $this->scanPropertyDefinitions($node->stmts);
            }
        }
    }
}
