<?php

namespace Crowbar\Ast\Visitor;

use Crowbar\Ast\Manager\ClassNameManager;
use Doctrine\Common\Annotations\ImplicitlyIgnoredAnnotationNames;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Class LicenceRemoveVisitor
 * @package Crowbar\Ast\Visitor
 */
class UselessCommentRemoveVisitor extends NodeVisitorAbstract
{
    /**
     * @var array
     */
    private $ignoredAnnotations;

    public function __construct()
    {
        $this->ignoredAnnotations = array_merge([

        ], array_keys(ImplicitlyIgnoredAnnotationNames::LIST));
    }

    /**
     * @param Node $node
     * @return int|Node|Node[]|void|null
     */
    public function leaveNode(Node $node)
    {
        if($node instanceof Node\Stmt\ClassMethod || $node instanceof Node\Stmt\Property){
            $commentObject = $node->getDocComment();

            if(!$commentObject){
                return $node;
            }

            $docComment = $commentObject->getText();

            $commentLines = explode("\n", $docComment);

            $commentLines = array_map("trim", $commentLines);

            $linesToKeep = [];

            foreach($commentLines as $commentLine){
                if(preg_match("#\*( |)(@\w+)#", $commentLine, $matches)){
                    //line has an annotation
                    $annotation = str_replace("@", "", $matches[2]);

                    if(!in_array($annotation, $this->ignoredAnnotations)){
                        $linesToKeep[] = $commentLine;
                    }
                }
            }

            if(count($linesToKeep) > 0){
                $newComments = [
                    array_shift($commentLines)
                ];

                $newComments  = array_merge($newComments, $linesToKeep);

                $newComments[] = array_pop($commentLines);

                $node->setDocComment(new Doc(implode("\n", $newComments)));
            }else{
                $node->setDocComment(new Doc(""));
            }
        }

        if($node->hasAttribute("comments")){
            foreach($node->getAttribute("comments") as $commentObject){
                $commentText = $commentObject->getText();

                if(strpos($commentText, "//") !== false){
                    $node->setAttribute("comments", []);
                    break;
                }
            }
        }

        return $node;
    }
}