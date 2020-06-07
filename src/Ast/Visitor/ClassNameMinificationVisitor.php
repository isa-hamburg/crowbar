<?php

namespace Crowbar\Ast\Visitor;

use Crowbar\Ast\Manager\ClassNameManager;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Class ClassNameMinificationVisitor
 * @package Crowbar\Ast\Visitor
 */
class ClassNameMinificationVisitor extends NodeVisitorAbstract
{
    /**
     * @var ClassNameManager
     */
    private $classNameManager;

    /**
     * ClassNameMinificationVisitor constructor.
     * @param ClassNameManager $classNameManager
     */
    public function __construct(ClassNameManager $classNameManager)
    {
        $this->classNameManager = $classNameManager;
    }

    /**
     * @param Node $node
     * @return int|Node|Node[]|void|null
     */
    public function leaveNode(Node $node)
    {
        if($node instanceof Node\Stmt\Namespace_){
            $this->classNameManager->minifyClassName($node);

            return $this->classNameManager->minifyNamespace($node);
        }
    }
}