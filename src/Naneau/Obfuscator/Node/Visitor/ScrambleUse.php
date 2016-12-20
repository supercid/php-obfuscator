<?php
/**
 * ScrambleUse.php
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */

namespace Naneau\Obfuscator\Node\Visitor;

use Naneau\Obfuscator\Node\Visitor\TrackingRenamerTrait;
use Naneau\Obfuscator\Node\Visitor\SkipTrait;

use Naneau\Obfuscator\Node\Visitor\Scrambler as ScramblerVisitor;

use PhpParser\Node;

use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;

use PhpParser\Node\Param;

use PhpParser\Node\Stmt\Class_ as ClassStatement;
use PhpParser\Node\Stmt\Use_ as UseStatement;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\StaticVar;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\New_ as NewExpression;
use PhpParser\Node\Expr\Instanceof_ as InstanceOfExpression;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;

use PhpParser\Node\Expr\Variable;

/**
 * ScrambleUse
 *
 * This scrambler assumes that all names are fully resolved by the
 * NameResolver.
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
class ScrambleUse extends ScramblerVisitor
{
    use SkipTrait;
    use TrackingRenamerTrait;

    /**
     * @var bool Add uses of fully qualified in-line uses.
     */
    private $addAsUse;

    /**
     * @var bool Do not reuse an use statement. A second use is added if needed.
     */
    private $doNotReuse;

    /**
     * @var array List of use statements to insert (in the leaveNode and
     *            afterTraverse method).
     */
    private $inserts;

    /**
     * Before node traversal
     *
     * @param  Node[] $nodes
     * @return array
     **/
    public function beforeTraverse(array $nodes)
    {
        // Reset renamed list
        $this->resetRenamed();

        // Reset list of inserts
        $this->inserts = [];

        // Scan for use statements
        $this->scanUse($nodes);

        return $nodes;
    }

    /**
     * Add use statements that are global (e.g. not in a namespace).
     *
     * @param  Node[] $nodes
     * @return array
     */
    public function afterTraverse(array $nodes)
    {
        $key = "";

        if (isset($this->inserts[$key])) {
            $nodes = array_merge($this->inserts[$key], $nodes);

            return $nodes;
        }
    }

    /**
     * Check all variable nodes
     *
     * @param  Node $node
     * @return void
     */
    public function enterNode(Node $node)
    {
        // Class statements
        if ($node instanceof ClassStatement) {
            // Classes that extend another class
            if ($node->extends !== null) {
                $node->extends = $this->rename($node->extends, $node->meta);
            }

            // Classes that implement an interface
            if ($node->implements && count($node->implements) > 0) {
                $implements = [];

                foreach ($node->implements as $implement) {
                    $implements[] = $this->rename($implement, $node->meta);
                }

                $node->implements = $implements;
            }

            return $node;
        }

        // Param rename
        if ($node instanceof Param && $node->type instanceof Name) {
            $node->type = $this->rename($node->type, $node->meta);
            return $node;
        }

        // Static call or constant lookup on class
        if ($node instanceof ClassConstFetch
            || $node instanceof StaticCall
            || $node instanceof StaticPropertyFetch
            || $node instanceof StaticVar
            || $node instanceof NewExpression
            || $node instanceof InstanceOfExpression
        ) {
            // We need a name
            if (!($node->class instanceof Name)) {
                return;
            }

            $node->class = $this->rename($node->class, $node->meta);
            return $node;
        }

        // Trait uses
        if ($node instanceof TraitUse) {
            $traits = [];

            foreach ($node->traits as $trait) {
                $traits[] = $this->rename($trait, $node->meta);
            }

            $node->traits = $traits;
            return $node;
        }
    }

    /**
     * Add use statements to namespace statements.
     *
     * @param  Node $node
     * @return void
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof NamespaceNode) {
            $key = $node->name->toString();

            if (isset($this->inserts[$key])) {
                $node->stmts = array_merge($this->inserts[$key], $node->stmts);

                return $node;
            }
        }
    }

    /**
     * Scramble at use statements
     *
     * @param  Node[] $nodes
     * @return void
     **/
    private function scanUse(array $nodes)
    {
        foreach ($nodes as $node) {
            // Scramble the private method definitions
            if ($node instanceof UseStatement || $node instanceof GroupUse) {
                foreach ($node->uses as $useNode) {
                    // Record original name and scramble it
                    if ($node instanceof GroupUse) {
                        $originalName = $node->prefix . "\\" . $useNode->name->toString();
                    } else {
                        $originalName = $useNode->name->toString();
                    }

                    // Scramble into new use name
                    // $newName = $this->scrambleString(
                    //     $originalName . '-' . $useNode->alias
                    // );

                    // Record renaming of full class
                    // $this->renamed($originalName, $newName);

                    // Record renaming of alias
                    // $this->renamed($useNode->alias, $newName);

                    // Set the new alias
                    // $useNode->alias = $newName;
                }
            }

            // Recurse over child nodes
            if (isset($node->stmts) && is_array($node->stmts)) {
                $this->scanUse($node->stmts);
            }
        }
    }

    /**
     * Rename a given name node.
     *
     * When $this->addAsUse, a new use statement is added for each name that is
     * not renamed.
     *
     * @param Name $name Name node to rename.
     * @param Meta $meta Node meta information.
     * @return Name Renamed node if renamed, otherwise original.
     */
    private function rename(Name $name, Meta $meta)
    {
        $originalName = $name->toString();

        // Skip references to self or parent.
        if ($originalName == "self" || $originalName == "parent") {
            return $name;
        }

        // Either add-as-use or fix uses.
        // if ($this->addAsUse) {

        //     // Check if there is a existing rename.
        //     if (!$this->isRenamed($originalName) || $this->doNotReuse) {
        //         $scrambledName = $this->scrambleString($originalName . uniqid());
        //         $this->renamed($originalName, $scrambledName);

        //         // A new use statement is needed for this rename. Add it to
        //         // the current namespace.
        //         if ($meta->namespace) {
        //             $key = $meta->namespace->name->toString();
        //         } else {
        //             $key = "";
        //         }

        //         if (!isset($this->inserts[$key])) {
        //             $this->inserts[$key] = [];
        //         }

        //         $this->inserts[$key][] = new UseStatement([
        //             new UseUse(new Name($originalName), $scrambledName)
        //         ]);
        //     }

        //     return new Name($this->getNewName($originalName));
        // } else {
        //     // See if (a subset of) parts matches a renamed one. For instance,
        //     // A\B\C\D may have A\B renamed, which will eventually lead to
        //     // Renamed\C\D.
        //     $clonedName = clone $name;
        //     $removed = [];

        //     while ($clonedName->parts) {
        //         $originalName = $clonedName->toString();

        //         if ($this->isRenamed($originalName)) {
        //             array_unshift($removed, $this->getNewName($originalName));
        //             return new Name($removed);
        //         }

        //         // Remove one name from the parts list and try again.
        //         $removed[] = array_pop($clonedName->parts);
        //     }
        // }

        // No luck.
        return $name;
    }

    /**
     * Get the value of Add As Use
     *
     * @return bool Add uses of fully qualified in-line uses.
     */
    public function getAddAsUse()
    {
        return $this->addAsUse;
    }

    /**
     * Set the value of Add As Use
     *
     * @param bool $addAsUse Add uses of fully qualified in-line uses.
     *
     * @return self
     */
    public function setAddAsUse($addAsUse)
    {
        $this->addAsUse = $addAsUse;

        return $this;
    }

    /**
     * Get the value of Do Not Reuse
     *
     * @return bool Do not reuse an use statement. A second use is added if needed.
     */
    public function getDoNotReuse()
    {
        return $this->doNotReuse;
    }

    /**
     * Set the value of Do Not Reuse
     *
     * @param bool $doNotReuse Do not reuse an use statement. A second use is added if needed.
     *
     * @return self
     */
    public function setDoNotReuse($doNotReuse)
    {
        $this->doNotReuse = $doNotReuse;

        return $this;
    }
}
