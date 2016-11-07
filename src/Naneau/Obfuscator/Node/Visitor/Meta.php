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
use PhpParser\Node\Stmt\Use_ as UseNode;
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
    /**
     * @var NamespaceNode The current namespace that is visited.
     */
    public $namespace;

    /**
     * @var array Array of use nodes that have been encountered.
     */
    public $uses;

    /**
     * @var UseNode The last use statement that is encountered;
     */
    public $use;

    /**
     * @var ClassNode The current class that is visited.
     */
    public $class;

    /**
     * @var array An array of scopes.
     */
    public $scopes;

    /**
     * @var mixed Current scope (last one in the list of scopes).
     */
    public $scope;

    /**
     * @var Node. The first node
     */
    public $root;

    /**
     * Before node traversal
     *
     * @param  Node[] $nodes
     * @return array
     **/
    public function beforeTraverse(array $nodes)
    {
        ($nodes);
        
        $this->namespace = null;
        $this->uses = [];
        $this->use = null;
        $this->class = null;
        $this->scopes = [];
        $this->scope = null;
        $this->root = null;
    }

    /**
     * Add node meta data.
     *
     * @param  Node $node
     * @return void
     **/
    public function enterNode(Node $node)
    {
        if (!$this->root) {
            $this->root = $node;
        }

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
        } elseif ($node instanceof UseNode) {
            $this->use = $node;
            array_push($this->uses, $this->use);
        }

        // Each node gets access to this class.
        $node->meta = $this;
    }

    /**
     * Remove node meta data when leaving specific nodes.
     *
     * @param  Node $node
     * @return void
     **/
    public function leaveNode(Node $node)
    {
        if ($node instanceof NamespaceNode) {
            $this->namespace = null;
            $this->uses = [];
            $this->use = null;
        } elseif ($node instanceof ClassNode) {
            $this->class = null;
        } elseif ($node instanceof ClassMethod) {
            array_pop($this->scopes);
        }
    }
}
