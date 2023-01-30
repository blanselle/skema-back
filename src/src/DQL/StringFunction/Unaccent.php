<?php

declare(strict_types=1);

namespace App\DQL\StringFunction;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\InputParameter;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\AST\Node;

class Unaccent extends FunctionNode
{
    private null|PathExpression|InputParameter|Node $string = null;

    /**
     * @inheritDoc
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'UNACCENT(' . $this->string->dispatch($sqlWalker) . ')';
    }

    /**
     * @inheritDoc
     */
    public function parse(Parser $parser): void
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->string = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
