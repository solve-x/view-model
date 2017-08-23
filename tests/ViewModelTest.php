<?php

use Carbon\Carbon;

require __DIR__ . '/RegistrationViewModel.php';
require __DIR__ . '/ApiTokenViewModel.php';
require __DIR__ . '/AfterViewModel.php';
require __DIR__ . '/InViewModel.php';
require __DIR__ . '/Request.php';

class ViewModelTest extends PHPUnit_Framework_TestCase
{
    public function test_properties_are_set()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => '19',
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertTrue($model->IsValid);

        $this->assertSame('Jack', $model->FirstName);
        $this->assertSame('Smith', $model->LastName);
        $this->assertSame(19, $model->Age);
        $this->assertSame('my password', $model->Password);
        $this->assertSame('my password', $model->RepeatedPassword);
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

    public function test_invalid_property_errors()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => 15,
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertFalse($model->IsValid);
        $this->assertArrayHasKey('Age', $model->Errors);
        $this->assertCount(2, $model->Errors['Age']);
        $this->assertEquals('Value not an int!', $model->Errors['Age'][0]);
        $this->assertEquals('Value less than min required!', $model->Errors['Age'][1]);
    }

    public function test_compare_annotation()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => '18',
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertTrue($model->IsValid);

        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => '18',
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
            'Age' => '18',
            'Password' => 'my',
            'RepeatedPassword' => 'my',
        ]));

        $this->assertFalse($model->IsValid);
        $this->assertNull($model->Password);
    }

    public function test_carbon()
    {
        $model = new ApiTokenViewModel(new Request([
            'Value' => '0987654321234567',
            'ValidFrom' => '2017-06-01',
        ]));

        $this->assertTrue($model->IsValid);
        $this->assertTrue($model->ValidFrom instanceof Carbon);
        $this->assertEquals($model->ValidFrom, new Carbon('2017-06-01'));
    }

    /**
     * @expectedException \SolveX\ViewModel\ValidationException
     */
    public function test_throwable_viewmodel()
    {
        new ApiTokenViewModel(new Request([
            'Value' => 'too short',
            'ValidFrom' => '2017-06-01',
        ]));
    }

    /**
     * @expectedException \SolveX\ViewModel\ValidationException
     */
    public function test_throwable_viewmodel_invalid_date_format()
    {
        new ApiTokenViewModel(new Request([
            'Value' => '0987654321234567',
            'ValidFrom' => '1.6.2017',
        ]));
    }

    public function test_after()
    {
        $model = new AfterViewModel(new Request([
            'DateOfArrival' => '2017-06-03 08:00:00',
        ]));

        $this->assertTrue($model->IsValid);
        $this->assertTrue($model->DateOfArrival instanceof Carbon);
        $this->assertEquals($model->DateOfArrival, new Carbon('2017-06-03 08:00:00'));

        $model = new AfterViewModel(new Request([
            'DateOfArrival' => '2017-01-01',
        ]));

        // Invalid date format.
        $this->assertFalse($model->IsValid);
        $this->assertNull($model->DateOfArrival);

        $model = new AfterViewModel(new Request([
            'DateOfArrival' => '3.6.2017',
        ]));

        $this->assertFalse($model->IsValid);
        $this->assertNull($model->DateOfArrival);
    }

    public function test_in()
    {
        $model = new InViewModel(new Request([
            'Place' => 'New York',
        ]));

        $this->assertTrue($model->IsValid);
        $this->assertSame($model->Place, 'New York');

        $model = new InViewModel(new Request([
            'Place' => 'Tokyo',
        ]));

        $this->assertFalse($model->IsValid);
        $this->assertNull($model->Place);
    }
}