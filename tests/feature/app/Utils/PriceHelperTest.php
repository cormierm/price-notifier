<?php

namespace Tests\Feature\App\Utils;

use App\Utils\PriceHelper;
use Tests\TestCase;

class PriceHelperTest extends TestCase
{

    /** @test */
    public function itCanParseNumbersFromText(): void
    {
        $text = 'CDN$ 23,432,4343.334';

        $this->assertEquals(234324343.33, PriceHelper::numbersFromText($text));
    }

    /** @test */
    public function itCanParseNumbersFromTextWithNbsp(): void
    {
        $text = 'CDN$&nbsp;2.48';

        $this->assertEquals(2.48, PriceHelper::numbersFromText($text));
    }

    /** @test */
    public function itReturnsEmptyStringWhenPassedEmptyString(): void
    {
        $text = '';

        $this->assertEquals('', PriceHelper::numbersFromText($text));
    }
}
