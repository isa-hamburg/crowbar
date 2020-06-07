<?php


namespace Crowbar;


use Crowbar\Ast\NodeTraverser;
use Crowbar\Ast\Visitor\LicenceRemoveVisitor;
use Crowbar\Ast\Visitor\UselessCommentRemoveVisitor;
use Crowbar\Command\MinifyCommand;
use Crowbar\Filesystem\SizeComputer;
use Crowbar\Service\MinificationService;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class Application
 * @package Crowbar
 */
class Application extends \Symfony\Component\Console\Application
{
    /**
     * @return Application
     */
    public function setup(): self
    {
        $this->add(new MinifyCommand(new MinificationService(
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
            new Standard(),
            new NodeTraverser([
                //new ClassNameMinificationVisitor(new ClassNameManager())
                new LicenceRemoveVisitor(),
                new UselessCommentRemoveVisitor()
            ]),
            new SizeComputer(
                new SizeComputer\WinAdapter(),
                new SizeComputer\LinuxAdapter()
            )
        )));

        return $this;
    }
}