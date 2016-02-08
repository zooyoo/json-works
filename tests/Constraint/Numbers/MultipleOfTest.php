<?php

namespace JsonWorks\Tests\Constraint\Numbers;

class MultipleOfTest extends \JsonWorks\Tests\Base
{
    public function testIntegerValid()
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 5}
            }
        }';

        $data = '{
            "test": 25
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testIntegerInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 5}
            }
        }';

        $data = '{
            "test": 26
        }';

        $this->assertFalse($this->validate($schema, $data));
    }

    public function testFloatValid()
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 5.4}
            }
        }';

        $data = '{
            "test": 54
        }';

        $this->assertTrue($this->validate($schema, $data));
    }

    public function testFloatInvalid()
    {
        $schema = '{
            "properties": {
                "test": {"multipleOf": 5.4}
            }
        }';

        $data = '{
            "test": 53.999
        }';

        $this->assertFalse($this->validate($schema, $data));
    }
}
