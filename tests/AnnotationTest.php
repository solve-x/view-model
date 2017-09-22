<?php

use SolveX\ViewModel\KeyValueDataSource;

require_once __DIR__ . '/RegistrationViewModel.php';
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
        $model = new ArraysViewModel(new Request([
            'IdsArray' => [1, 2, 3],
            'PricesArray' => [15.4, -8.3],
            'NamesArray' => []
        ]));

        $this->assertFalse($model->isValid());
        $this->assertNull($model->NamesArray);
    }
}