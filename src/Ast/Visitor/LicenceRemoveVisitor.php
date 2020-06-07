<?php

namespace Crowbar\Ast\Visitor;

use Crowbar\Ast\Manager\ClassNameManager;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Class LicenceRemoveVisitor
 * @package Crowbar\Ast\Visitor
 */
class LicenceRemoveVisitor extends NodeVisitorAbstract
{
    /**
     * @param Node $node
     * @return int|Node|Node[]|void|null
     */
    public function leaveNode(Node $node)
    {
        if($node instanceof Node\Stmt\Namespace_){
            $node->setDocComment(new Doc(""));
        }

        return $node;
    }
}