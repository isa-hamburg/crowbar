<?php


namespace Crowbar\Ast\Manager;


use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;

/**
 * Class ClassNameManager
 * @package Crowbar\Ast\Manager
 */
class ClassNameManager
{
    /**
     * @var array
     */
    private $classMap = [];

    /**
     * @var array
     */
    private $partsMap = [];

    /**
     * @param Namespace_ $namespace_
     * @return Namespace_
     */
    public function minifyNamespace(Namespace_ $namespace_): Namespace_
    {
        $namespace = implode("\\", $namespace_->name->parts);

        if(!isset($this->classMap[$namespace])){
            foreach($namespace_->name->parts as $key => $part){
                $namespace_->name->parts[$key] = $this->getPartMinification($part);
            }

            $this->classMap[$namespace] = $namespace_;
        }

        return $this->classMap[$namespace];
    }

    /**
     * @param Namespace_ $namespace_
     * @return Namespace_
     */
    public function minifyClassName(Namespace_ $namespace_): Namespace_
    {
        $namespace = implode("\\", $namespace_->name->parts);

        foreach($namespace_->stmts as $stmt){
            switch (true) {
                case $stmt instanceof Interface_:
                case $stmt instanceof Class_:
                case $stmt instanceof Trait_:
                    $stmt->name = $this->getPartMinification($stmt->name, $namespace);
                    break;
            }
        }

        return $namespace_;
    }

    /**
     * @param string $part
     * @param string $namespace
     * @return string
     */
    private function getPartMinification(string $part, $namespace = ""): string
    {
        if(!isset($this->partsMap[$namespace])){
            $this->partsMap[$namespace] = [];
        }

        if(!isset($this->partsMap[$namespace][$part])){
            $minifiedPart = "";

            for($i = 97; $i <= 122; $i++){
                $minifiedPart .= chr($i);

                if(!in_array($minifiedPart, $this->partsMap[$namespace])){
                    $this->partsMap[$namespace][$part] = $minifiedPart;
                    break;
                }
            }
        }

        return $this->partsMap[$namespace][$part];
    }
}