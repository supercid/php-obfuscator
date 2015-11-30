<?php
/**
 * Meta.php
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */

namespace Naneau\Obfuscator\Node\Visitor;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_ as NewNode;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;
use PhpParser\NodeVisitorAbstract;

/**
 * Meta
 *
 * Adds meta data to each node, such as current class, namespace and scope.
 *
 * @category        Naneau
 * @package         Obfuscator
 * @subpackage      NodeVisitor
 */
class Meta extends NodeVisitorAbstract
{
    public $namespace;

    public $class;

    public $scopes = [];

    public $scope;

    /**
     * Add node meta data.
     *
     * @param  Node $node
     * @return void
     **/
    public function enterNode(Node $node)
    {
        if ($node instanceof NamespaceNode) {
            $this->namespace = $node;
        } elseif ($node instanceof ClassNode) {
            $this->class = $node;
        } elseif ($node instanceof ClassMethod) {
            $this->scope = [];
            array_push($this->scopes, $this->scope);

            // Add function parameter to scope.
            foreach ($node->params as $param) {
                $this->scope[$param->name] = $param->type;
            }
        } elseif ($node instanceof Assign) {
            if ($node->var instanceof Variable) {
                if ($node->expr instanceof Variable) {
                    if (isset($this->scope[$node->expr->name])) {
                        $this->scope[$node->var->name] = $this->scope[$node->expr->name];
                    }
                } elseif ($node->expr instanceof NewNode) {
                    if ($node->expr->class instanceof Name) {
                        $this->scope[$node->var->name] = $node->expr->class;
                    }
                }
            }
        }

        // Each node gets access to this class.
        $node->meta = $this;
    }

    /**
     * Remove node meta data.
     *
     * @param  Node $node
     * @return void
     **/
    public function leaveNode(Node $node)
    {
        if ($node instanceof NamespaceNode) {
            $this->namespace = null;
        } elseif ($node instanceof ClassNode) {
            $this->class = null;
        } elseif ($node instanceof ClassMethod) {
            array_pop($this->scopes);
        }
    }
}
