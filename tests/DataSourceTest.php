<?php

use SolveX\ViewModel\KeyValueDataSource;

class DataSourceTest extends \PHPUnit_Framework_TestCase
{
    public function test_key_value_data_source_get()
    {
        $source = new KeyValueDataSource([
           'prop' => 'some_value'
        ]);

        $this->assertEquals('some_value', $source->get('prop'));
    }

    public function test_key_value_data_source_has()
    {
        $source = new KeyValueDataSource([
            'prop' => 'some_value'
        ]);

        $this->assertTrue($source->has('prop'));
        $this->assertFalse($source->has('pop'));
    }
}