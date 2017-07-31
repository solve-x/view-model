<?php

require __DIR__ . '/RegistrationViewModel.php';
require __DIR__ . '/Request.php';

class ViewModelTest extends PHPUnit_Framework_TestCase
{
    public function testRegistrationViewModel()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => 19,
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertTrue($model->IsValid);

        $this->assertEquals('Jack', $model->FirstName);
        $this->assertEquals('Smith', $model->LastName);
        $this->assertEquals(19, $model->Age);
        $this->assertEquals('my password', $model->Password);
        $this->assertEquals('my password', $model->RepeatedPassword);
    }

    public function testRegistrationViewModelAge()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => 15,
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertFalse($model->IsValid);
    }
}