<?php
/**
 * ScramblePrivateMethod.php
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
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\New_ as NewNode;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * ScramblePrivateMethod
 *
 * Renames private methods
 *
 * WARNING
 *
 * This method is not foolproof. This visitor scans for all private method
 * declarations and renames them. It then finds *all* method calls in the
 * class, and renames them if they match the name of a renamed method. If your
 * class calls a method of *another* class that happens to match one of the
 * renamed private methods, this visitor will rename it.
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
class ScramblePrivateMethod extends ScramblerVisitor
{
    use SkipTrait;
    use TrackingRenamerTrait;

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
            ->skip($this->variableMethodCallsUsed($nodes));

        $this->scanMethodDefinitions($nodes);

        return $nodes;
    }

    /**
     * Check all variable nodes
     *
     * @param  Node $node
     * @return null|Node Scrambled node, if needed
     **/
    public function enterNode(Node $node)
    {
        if ($this->shouldSkip()) {
            return;
        }

        // Scramble calls
        if ($node instanceof MethodCall) {

            // Method call is not calling private methods
            if (!$this->isPrivateMethodCall($node)) {
                return;
            }

            // Node wasn't renamed
            if (!$this->isRenamed($node->name)) {
                return;
            }

            // Scramble usage
            return $this->scramble($node);
        }
    }

    /**
     * Check if a given method call is calling a private method
     *
     * @param  MethodCall $node
     * @return bool
     **/
    private function isPrivateMethodCall(MethodCall $node)
    {
        $meta = $node->meta;

        // It's never a private method call outside a class.
        if (!$meta->class) {
            return false;
        }

        // $this always points to current instance.
        if ($node->var instanceof Variable && $node->var->name === "this") {
            return true;
        }

        // A variable is pointing to same instance or same class.
        if ($node->var instanceof Variable) {
            if (isset($meta->scope[$node->var->name])) {
                if ($meta->scope[$node->var->name] instanceof Name) {
                    if ($meta->scope[$node->var->name]->toString() === $meta->class->name) {
                        return true;
                    }
                }
            }
        }

        // Same class is created and directly used.
        if ($node->var instanceof NewNode && $node->var->class instanceof Name) {
            if ($meta->class->name === $node->var->class->toString()) {
                return true;
            }
        }

        // Not sure what type of object is, so return false.
        return false;
    }

    /**
     * Recursively scan for method calls and see if variables are used
     *
     * @param  Node[] $nodes
     * @return void
     **/
    private function variableMethodCallsUsed(array $nodes)
    {
        foreach ($nodes as $node) {
            if ($node instanceof MethodCall && $node->name instanceof Variable) {
                // A method call uses a Variable as its name
                return true;
            }

            // Recurse over child nodes
            if (isset($node->stmts) && is_array($node->stmts)) {
                $used = $this->variableMethodCallsUsed($node->stmts);

                if ($used) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Recursively scan for private method definitions and rename them
     *
     * @param  Node[] $nodes
     * @return void
     **/
    private function scanMethodDefinitions(array $nodes)
    {
        foreach ($nodes as $node) {
            // Scramble the private method definitions
            if ($node instanceof ClassMethod && ($node->type & ClassNode::MODIFIER_PRIVATE)) {

                // Record original name and scramble it
                $originalName = $node->name;
                $this->scramble($node);

                // Record renaming
                $this->renamed($originalName, $node->name);
            }

            // Recurse over child nodes
            if (isset($node->stmts) && is_array($node->stmts)) {
                $this->scanMethodDefinitions($node->stmts);
            }
        }
    }
}
