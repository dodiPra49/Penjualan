<?php

namespace Tests\Unit;

use App\Services\SequenceGeneratorService;
use Tests\TestCase;

class SequenceGeneratorServiceTest extends TestCase
{
    protected SequenceGeneratorService $sequenceGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sequenceGenerator = new SequenceGeneratorService();
    }

    /**
     * Uji pemformatan kode dengan prefix dan padding digit.
     */
    public function test_format_code_properly(): void
    {
        $code1 = $this->sequenceGenerator->formatCode('PLG-', 1, 4);
        $this->assertEquals('PLG-0001', $code1);

        $code99 = $this->sequenceGenerator->formatCode('PLG-', 99, 4);
        $this->assertEquals('PLG-0099', $code99);

        $codeInv = $this->sequenceGenerator->formatCode('INV-', 12345, 6);
        $this->assertEquals('INV-012345', $codeInv);
    }

    /**
     * Uji fungsi generate dengan fallback aman ketika Redis offline.
     */
    public function test_generate_fallback_when_redis_offline(): void
    {
        // Simulasi max sequence di database adalah 15
        $mockMaxResolver = function () {
            return 15;
        };

        $result = $this->sequenceGenerator->generate(
            prefix: 'PLG-',
            sequenceKey: 'test_pelanggan',
            maxDbResolver: $mockMaxResolver,
            padLength: 4
        );

        // Jika Redis offline, sistem fallback ke $mockMaxResolver() + 1 = 16
        // Jika Redis online, inisialisasi 15 lalu INCR = 16
        $this->assertEquals('PLG-0016', $result);
    }

    /**
     * Uji generate dari awal (ketika database masih kosong / 0).
     */
    public function test_generate_first_sequence(): void
    {
        $mockZeroResolver = function () {
            return 0;
        };

        $result = $this->sequenceGenerator->generate(
            prefix: 'PLG-',
            sequenceKey: 'test_empty_sequence',
            maxDbResolver: $mockZeroResolver,
            padLength: 4
        );

        $this->assertEquals('PLG-0001', $result);
    }

    /**
     * Uji alur Redis Atomic INCR ketika Redis aktif dan key sudah ada.
     */
    public function test_generate_using_redis_atomic_incr(): void
    {
        \Illuminate\Support\Facades\Redis::shouldReceive('exists')
            ->once()
            ->with('sequence:active_redis')
            ->andReturn(true);

        \Illuminate\Support\Facades\Redis::shouldReceive('incr')
            ->once()
            ->with('sequence:active_redis')
            ->andReturn(42);

        $result = $this->sequenceGenerator->generate(
            prefix: 'PLG-',
            sequenceKey: 'active_redis',
            maxDbResolver: function () {
                return 0;
            },
            padLength: 4
        );

        $this->assertEquals('PLG-0042', $result);
    }
}
