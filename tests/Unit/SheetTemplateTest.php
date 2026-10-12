<?php
namespace App\Tests\Unit;

use App\Domain\SheetTemplate;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SheetTemplateTest extends TestCase
{
    private array $fields = [
        ['key' => 'strength', 'label' => 'Force', 'type' => 'number'],
        ['key' => 'alive', 'label' => 'Vivant', 'type' => 'boolean'],
        ['key' => 'story', 'label' => 'Histoire', 'type' => 'text'],
    ];
    public function testAcceptsTypedValuesAndOptionalFields(): void
    {
        self::assertSame($this->fields, SheetTemplate::fields($this->fields));
        $values = ['strength' => 12, 'alive' => false, 'story' => 'Voyageur'];
        self::assertSame($values, SheetTemplate::values($values, $this->fields));
        self::assertSame([], SheetTemplate::values([], $this->fields));
    }
    public function testDuplicateKeysAreRejected(): void
    {
        $this->expectException(BadRequestHttpException::class);
        SheetTemplate::fields([$this->fields[0], $this->fields[0]]);
    }
    public function testUnknownFieldIsRejected(): void
    {
        $this->expectException(BadRequestHttpException::class);
        SheetTemplate::values(['admin' => true], $this->fields);
    }
    public function testNumbersCannotBeSubmittedAsText(): void
    {
        $this->expectException(BadRequestHttpException::class);
        SheetTemplate::values(['strength' => '12'], $this->fields);
    }
    public function testBooleanIsStrictlyTyped(): void
    {
        $this->expectException(BadRequestHttpException::class);
        SheetTemplate::values(['alive' => 1], $this->fields);
    }
}
