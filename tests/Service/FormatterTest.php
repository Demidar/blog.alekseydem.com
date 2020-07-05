<?php

namespace App\Tests\Service;

use App\Service\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    /**
     * @dataProvider getCamelAndPascalCases
     */
    public function testHumanize($expected, $stringOrArray)
    {
        $formatted = Formatter::humanize($stringOrArray);
        $this->assertEquals($expected, $formatted);
    }

    public function getCamelAndPascalCases(): iterable
    {
        yield 0 => ['Var name', 'varName'];
        yield 1 => ['Var name', 'VarName'];
        yield 2 => ['Var 2 name', 'var2Name'];
        yield 3 => ['Var n 2 ame', 'varN2ame'];
        yield 4 => ['V ar na me', 'vArNaMe'];
        yield 5 => ['V a 2 r 3 name 5', 'vA2r3Name5'];
        yield 6 => [['Var name', 'Var name'], ['varName', 'VarName']];
        yield 7 => [['Var 2 name', 'Var n 2 ame', 'V ar na me'], ['var2Name', 'varN2ame', 'vArNaMe']];
        yield 8 => [['Рус 2 символы', 'Стр о 2 ка', 'С тр оч ка'], ['рус2Символы', 'СтрО2ка', 'сТрОчКа']];
    }
}
