<?php
/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */


namespace PHPMD\Rule\CleanCode;

use PDepend\Source\AST\AbstractASTNode;
use PDepend\Source\AST\ASTArrayElement;
use PDepend\Source\AST\ASTLiteral;
use PDepend\Source\AST\ASTNode as PDependASTNode;
use PHPMD\AbstractNode;
use PHPMD\AbstractRule;
use PHPMD\Node\ASTNode;
use PHPMD\Rule\FunctionAware;
use PHPMD\Rule\MethodAware;

/**
 * Duplicated Array Key Rule
 *
 * This rule detects if array literal has duplicated entries for any key.
 *
 * @author Rafał Wrzeszcz <rafal.wrzeszcz@wrzasq.pl>
 * @author Kamil Szymanaski <kamil.szymanski@gmail.com>
 */
class DuplicatedArrayKey extends AbstractRule implements MethodAware, FunctionAware
{
    /**
     * This method checks if a given function or method contains an array literal
     * with duplicated entries for any key and emits a rule violation if so.
     *
     * @param AbstractNode $node
     * @return void
     */
    public function apply(AbstractNode $node)
    {
        foreach ($node->findChildrenOfType('Array') as $arrayNode) {
            /** @var ASTNode $arrayNode */
            $this->analyzeArray($arrayNode);
        }
    }

    /**
     * Analyzes single array.
     *
     * @param ASTNode $node Array node.
     * @return void
     */
    private function analyzeArray(ASTNode $node)
    {
        $keys = array();
        /** @var ASTArrayElement $arrayElement */
        foreach ($node->getChildren() as $index => $arrayElement) {
            $arrayElement = $this->normalizeKey($arrayElement, $index);
            if (null === $arrayElement) {
                // skip everything that can't be resolved easily
                continue;
            }
            $key = $arrayElement->getImage();
            if (isset($keys[$key])) {
                $this->addViolation($node, array($key, $arrayElement->getStartLine()));
                continue;
            }
            $keys[$key] = $arrayElement;
        }
    }

    /**
     * Sets normalized name as node's image.
     *
     * @param AbstractASTNode $node Array key to evaluate.
     * @param int $index Fallback in case of non-associative arrays
     * @return AbstractASTNode Key name
     */
    private function normalizeKey(AbstractASTNode $node, $index)
    {
        if (count($node->getChildren()) === 0) {
            $node->setImage((string) $index);
            return $node;
        }
        
        $node = $node->getChild(0);
        if (!($node instanceof ASTLiteral)) {
            // skip expressions, method calls, globals and constants
            return null;
        }
        $node->setImage($this->stringFromLiteral($node));

        return $node;
    }

    /**
     * Cleans string literals and casts boolean and null values as PHP engine does
     *
     * @param PDependASTNode $key
     * @return string
     */
    private function stringFromLiteral(PDependASTNode $key)
    {
        $value = $key->getImage();
        switch ($value) {
            case 'false':
                return '0';
            case 'true':
                return '1';
            case 'null':
                return '';
            default:
                return trim($value, '\'""');
        }
    }
}
