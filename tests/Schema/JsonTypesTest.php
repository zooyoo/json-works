<?php

namespace JsonWorks\Tests\Schema;

use JohnStevenson\JsonWorks\Schema\JsonTypes;

class JsonTypesTest extends \JsonWorks\Tests\Base
{
    protected $jsonTypes;

    protected function setUp()
    {
        $this->jsonTypes = new JsonTypes;
    }

    public function testGenericWithInteger()
    {
        $value = 23;
        $expected = 'number';

        $type = $this->jsonTypes->getGeneric($value);
        $this->assertEquals($expected, $type);
    }

    public function testGenericWithFloat()
    {
        $value = 23.7;
        $expected = 'number';

        $type = $this->jsonTypes->getGeneric($value);
        $this->assertEquals($expected, $type);
    }

    public function testArray()
    {
        $value = [1, 2, 3];
        $type = 'array';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertTrue($result);
    }

    public function testObject()
    {
        $value = (object) ['name' => 'value'];
        $type = 'object';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertTrue($result);
    }

    public function testNull()
    {
        $value = null;
        $type = 'null';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertTrue($result);
    }

    public function testBoolean()
    {
        $value = false;
        $type = 'boolean';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertTrue($result);
    }

    public function testString()
    {
        $value = 'test string';
        $type = 'string';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertTrue($result);
    }

    public function testIntegerAsInteger()
    {
        $value = 21;
        $type = 'integer';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertTrue($result);
    }

    public function testIntegerArbitrarilyLarge()
    {
        $value = PHP_INT_MAX + 100;
        $type = 'integer';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertTrue($result);
    }

    public function testIntegerAsNumber()
    {
        $value = 21;
        $type = 'number';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertTrue($result);
    }

    public function testFloatAsNumber()
    {
        $value = 21.2;
        $type = 'number';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertTrue($result);
    }

    public function testFloatNotInteger()
    {
        $value = 21.2;
        $type = 'integer';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertFalse($result);
    }

    public function testUnknown()
    {
        $value = [1, 2, 3];
        $type = 'unknown';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertFalse($result);
    }

    public function testFail()
    {
        $value = [1, 2, 3];
        $type = 'object';

        $result = $this->jsonTypes->checkType($value, $type);
        $this->assertFalse($result);
    }

    public function testArrayOfInteger()
    {
        $value = [100, -3, 4];
        $type = 'integer';

        $result = $this->jsonTypes->arrayOfType($value, $type);
        $this->assertTrue($result, 'Testing success');

        $value = [100, -3, 4.78];
        $result = $this->jsonTypes->arrayOfType($value, $type);
        $this->assertFalse($result, 'Testing failure');
    }

    public function testArrayOfNumber()
    {
        $value = [100, -3, 4.78];
        $type = 'number';

        $result = $this->jsonTypes->arrayOfType($value, $type);
        $this->assertTrue($result, 'Testing success');

        $value = [100, null, 4.78];
        $result = $this->jsonTypes->arrayOfType($value, $type);
        $this->assertFalse($result, 'Testing failure');
    }

    public function testArrayOfString()
    {
        $value = ['one', 'two', 'three'];
        $type = 'string';

        $result = $this->jsonTypes->arrayOfType($value, $type);
        $this->assertTrue($result, 'Testing success');

        $value = ['one', true, 'three'];
        $result = $this->jsonTypes->arrayOfType($value, $type);
        $this->assertFalse($result, 'Testing failure');
    }

    public function testArrayOfObject()
    {
        $object = new \stdClass();
        $value = [$object, $object, $object];
        $type = 'object';

        $result = $this->jsonTypes->arrayOfType($value, $type);
        $this->assertTrue($result, 'Testing success');

        $value = [$object, [], $object];
        $result = $this->jsonTypes->arrayOfType($value, $type);
        $this->assertFalse($result, 'Testing failure');
    }
}