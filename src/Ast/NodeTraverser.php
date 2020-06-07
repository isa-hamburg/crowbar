<?php


namespace Crowbar\Ast;

use PhpParser\{Node, NodeVisitorAbstract};
use \PhpParser\NodeTraverser as ParentNodeTraverser;

/**
 * Class NodeTraverser
 * @package Crowbar\Ast
 */
class NodeTraverser extends ParentNodeTraverser
{
    /**
     * NodeTraverser constructor.
     * @param array $visitors
     */
    public function __construct(array $visitors)
    {
        foreach($visitors as $visitor){
            $this->addVisitor($visitor);
        }
    }
}