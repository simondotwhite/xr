<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Xr\Tests;

use function Chevere\Message\message;
use Chevere\Throwable\Errors\TypeError;
use Chevere\Xr\XrThrowableParser;
use Exception;
use PHPUnit\Framework\TestCase;

final class XrThrowableParserTest extends TestCase
{
    public function testTopLevel(): void
    {
        $throwable = new Exception('foo');
        $parser = new XrThrowableParser($throwable, '');
        $this->assertSame(Exception::class, $parser->topic());
        $this->assertSame(
            Exception::class,
            $parser->throwableRead()->className()
        );
        $this->assertSame('⚠️Throwable', $parser->emote());
        $this->assertStringContainsString(Exception::class, $parser->body());
    }
    
    public function testNamespaced(): void
    {
        $throwable = new TypeError(message: message('foo'));
        $parser = new XrThrowableParser($throwable, '');
        $this->assertSame('TypeError', $parser->topic());
        $this->assertSame(
            TypeError::class,
            $parser->throwableRead()->className()
        );
        $this->assertStringContainsString(
            '<div class="throwable-message">foo</div>',
            $parser->body()
        );
    }

    public function testWithPrevious(): void
    {
        $throwable = new Exception('foo', previous: new Exception('bar'));
        $parser = new XrThrowableParser($throwable, '');
        $this->assertStringContainsString(
            '<div class="throwable-message">bar</div>',
            $parser->body()
        );
    }

    public function testWithExtra(): void
    {
        $extra = 'EXTRA EXTRA! TODD SMELLS';
        $throwable = new Exception('foo');
        $parser = new XrThrowableParser($throwable, $extra);
        $this->assertStringContainsString($extra, $parser->body());
    }
}
