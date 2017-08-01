<?php

require __DIR__ . '/RegistrationViewModel.php';
require __DIR__ . '/Request.php';

class ViewModelTest extends PHPUnit_Framework_TestCase
{
    public function test_properties_are_set()
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

    public function test_not_required()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            // Age missing.
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertTrue($model->IsValid);
        $this->assertNull($model->Age);
    }

    public function test_min_annotation()
    {
        $tests = [
            ['Age' => 15, 'Valid' => false],
            ['Age' => 18, 'Valid' => true],
            ['Age' => 19, 'Valid' => true],
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
                $this->assertTrue($model->IsValid);
            } else {
                $this->assertFalse($model->IsValid);
            }
        }
    }

    public function test_invalid_property_is_null()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => 15,
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertFalse($model->IsValid);
        $this->assertNull($model->Age);
    }

    public function test_compare_annotation()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => 18,
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertTrue($model->IsValid);

        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => 18,
            'Password' => 'my password',
            'RepeatedPassword' => 'password typo',
        ]));

        $this->assertFalse($model->IsValid);
        $this->assertNull($model->RepeatedPassword);
    }

    public function test_min_length_annotation()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => 18,
            'Password' => 'my',
            'RepeatedPassword' => 'my',
        ]));

        $this->assertFalse($model->IsValid);
        $this->assertNull($model->Password);
    }
}