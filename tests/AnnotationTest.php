<?php

use SolveX\ViewModel\KeyValueDataSource;
use SolveX\ViewModel\ValidationException;

require_once __DIR__ . '/RegistrationViewModel.php';
require_once __DIR__ . '/SimpleViewModel.php';
require_once __DIR__ . '/ArraysViewModel.php';
require_once __DIR__ . '/Request.php';

class AnnotationTest extends PHPUnit_Framework_TestCase
{
    public function test_required()
    {
        $model = new RegistrationViewModel(new KeyValueDataSource([
            //'FirstName' => 'Jack',
            'FirstName' => null,
            'LastName' => 'Smith',
            'Age' => '19',
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
            'RememberMe' => 'on',
        ]));

        $this->assertFalse($model->isValid());
    }

    public function test_no_empty_string()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => '  ', // Two spaces
            'LastName' => 'Smith',
            'Age' => '13',
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
            'RememberMe' => 'on',
        ]));

        $this->assertFalse($model->isValid());
        $this->assertNull($model->FirstName);
        $this->assertNull($model->Age);
        $this->assertEquals('Smith', $model->LastName);
    }

    public function test_no_empty_array()
    {
        $this->expectException(ValidationException::class);

        new ArraysViewModel(new Request([
            'IdsArray' => [1, 2, 3],
            'PricesArray' => [15.4, -8.3],
            'NamesArray' => []
        ]));
    }

    public function test_min_annotation()
    {
        $tests = [
            ['Age' => '15', 'Valid' => false],
            ['Age' => '18', 'Valid' => true],
            ['Age' => '19', 'Valid' => true],
        ];

        foreach ($tests as $test) {
            $model = new RegistrationViewModel(new Request([
                'FirstName' => 'Jack',
                'LastName' => 'Smith',
                'Age' => $test['Age'],
                'Password' => 'my password',
                'RepeatedPassword' => 'my password',
            ]));

            if ($test['Valid']) {
                $this->assertTrue($model->isValid());
            } else {
                $this->assertFalse($model->isValid());
            }
        }
    }

    public function test_default_value()
    {
        $model = new SimpleViewModel(new KeyValueDataSource([
            //'FirstName' => 'test'
        ]));
        $this->assertEquals('Joe', $model->FirstName);
        $this->assertTrue($model->isValid());

        $model = new SimpleViewModel(new KeyValueDataSource([
            'FirstName' => 'Jack'
        ]));
        $this->assertEquals('Jack', $model->FirstName);
        $this->assertTrue($model->isValid());
    }
}