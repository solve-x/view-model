<?php

use Carbon\Carbon;

require __DIR__ . '/RegistrationViewModel.php';
require __DIR__ . '/ApiTokenViewModel.php';
require __DIR__ . '/AfterViewModel.php';
require __DIR__ . '/InViewModel.php';
require __DIR__ . '/Request.php';

use Illuminate\Translation\Translator;

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
            'RememberMe' => 'on',
        ]));

        $this->assertTrue($model->isValid());

        $this->assertSame('Jack', $model->FirstName);
        $this->assertSame('Smith', $model->LastName);
        $this->assertSame(19, $model->Age);
        $this->assertSame('my password', $model->Password);
        $this->assertSame('my password', $model->RepeatedPassword);
        $this->assertSame(true, $model->RememberMe);
    }

    public function test_boolean()
    {
        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => '19',
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertTrue($model->isValid());

        $this->assertSame('Jack', $model->FirstName);
        $this->assertSame('Smith', $model->LastName);
        $this->assertSame(19, $model->Age);
        $this->assertSame('my password', $model->Password);
        $this->assertSame('my password', $model->RepeatedPassword);
        $this->assertSame(false, $model->RememberMe);
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

        $this->assertTrue($model->isValid());
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
                $this->assertTrue($model->isValid());
            } else {
                $this->assertFalse($model->isValid());
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

        $this->assertFalse($model->isValid());
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

        $this->assertFalse($model->isValid());
        $errors = $model->getErrors();
        $this->assertArrayHasKey('Age', $errors);
        $this->assertCount(2, $errors['Age']);
        $this->assertEquals('Value not an int!', $errors['Age'][0]);
        $this->assertEquals('Value less than min required!', $errors['Age'][1]);
    }

    public function test_invalid_property_translated_errors()
    {
        $translator = Mockery::mock(Translator::class);
        $translator->shouldReceive('trans')
            ->andReturnValues(['Vrednost ni število!', 'Vrednost manj od zahtevane!']);

        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => 15,
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]), $translator);

        $this->assertFalse($model->isValid());
        $errors = $model->getErrors();
        $this->assertArrayHasKey('Age', $errors);
        $this->assertCount(2, $errors['Age']);
        $this->assertEquals('Vrednost ni število!', $errors['Age'][0]);
        $this->assertEquals('Vrednost manj od zahtevane!', $errors['Age'][1]);
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

        $this->assertTrue($model->isValid());

        $model = new RegistrationViewModel(new Request([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => '18',
            'Password' => 'my password',
            'RepeatedPassword' => 'password typo',
        ]));

        $this->assertFalse($model->isValid());
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

        $this->assertFalse($model->isValid());
        $this->assertNull($model->Password);
    }

    public function test_carbon()
    {
        $model = new ApiTokenViewModel(new Request([
            'Value' => '0987654321234567',
            'ValidFrom' => '2017-06-01',
        ]));

        $this->assertTrue($model->isValid());
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

        $this->assertTrue($model->isValid());
        $this->assertTrue($model->DateOfArrival instanceof Carbon);
        $this->assertEquals($model->DateOfArrival, new Carbon('2017-06-03 08:00:00'));

        $model = new AfterViewModel(new Request([
            'DateOfArrival' => '2017-01-01',
        ]));

        // Invalid date format.
        $this->assertFalse($model->isValid());
        $this->assertNull($model->DateOfArrival);

        $model = new AfterViewModel(new Request([
            'DateOfArrival' => '3.6.2017',
        ]));

        $this->assertFalse($model->isValid());
        $this->assertNull($model->DateOfArrival);
    }

    public function test_in()
    {
        $model = new InViewModel(new Request([
            'Place' => 'New York',
        ]));

        $this->assertTrue($model->isValid());
        $this->assertSame($model->Place, 'New York');

        $model = new InViewModel(new Request([
            'Place' => 'Tokyo',
        ]));

        $this->assertFalse($model->isValid());
        $this->assertNull($model->Place);
    }
}
