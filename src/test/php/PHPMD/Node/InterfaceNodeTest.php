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

namespace PHPMD\Node;

use PDepend\Source\AST\ASTInterface;
use PDepend\Source\AST\ASTNamespace;
use PHPMD\AbstractTestCase;
use Sindelfingen\MyInterface;

/**
 * Test case for the interface node implementation.
 *
 * @covers \PHPMD\Node\AbstractTypeNode
 * @covers \PHPMD\Node\InterfaceNode
 */
class InterfaceNodeTest extends AbstractTestCase
{
    /**
     * testGetFullQualifiedNameReturnsExpectedValue
     */
    public function testGetFullQualifiedNameReturnsExpectedValue(): void
    {
        $interface = new ASTInterface('MyInterface');
        $interface->setNamespace(new ASTNamespace('Sindelfingen'));

        $node = new InterfaceNode($interface);

        static::assertSame(MyInterface::class, $node->getFullQualifiedName());
    }

    public function testGetConstantCountReturnsZeroByDefault(): void
    {
        $interface = new InterfaceNode(new ASTInterface('MyInterface'));
        static::assertSame(0, $interface->getConstantCount());
    }

    public function testGetConstantCount(): void
    {
        $class = $this->getInterface();
        static::assertSame(3, $class->getConstantCount());
    }

    public function testGetParentNameReturnsNull(): void
    {
        $interface = new InterfaceNode(new ASTInterface('MyInterface'));
        static::assertNull($interface->getParentName());
    }
}
