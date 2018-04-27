<?php

use Carbon\Carbon;

require_once __DIR__ . '/RegistrationViewModel.php';
require_once __DIR__ . '/ArraysViewModel.php';
require_once __DIR__ . '/AddressViewModel.php';

use SolveX\ViewModel\KeyValueDataSource;

class ViewModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @throws ReflectionException
     */
    public function test_properties_are_set()
    {
        $model = new RegistrationViewModel(new KeyValueDataSource([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => '19',
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
            'RememberMe' => 'on',
        ]));

        $this->assertSame('Jack', $model->FirstName);
        $this->assertSame('Smith', $model->LastName);
        $this->assertSame(19, $model->Age);
        $this->assertSame('my password', $model->Password);
        $this->assertSame('my password', $model->RepeatedPassword);
        $this->assertTrue($model->RememberMe);
    }

    /**
     * @throws ReflectionException
     */
    public function test_boolean()
    {
        $model = new RegistrationViewModel(new KeyValueDataSource([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            'Age' => '19',
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertSame('Jack', $model->FirstName);
        $this->assertSame('Smith', $model->LastName);
        $this->assertSame(19, $model->Age);
        $this->assertSame('my password', $model->Password);
        $this->assertSame('my password', $model->RepeatedPassword);
        $this->assertFalse($model->RememberMe);
    }

    /**
     * @throws ReflectionException
     */
    public function test_not_required()
    {
        $model = new RegistrationViewModel(new KeyValueDataSource([
            'FirstName' => 'Jack',
            'LastName' => 'Smith',
            // Age missing.
            'Password' => 'my password',
            'RepeatedPassword' => 'my password',
        ]));

        $this->assertNull($model->Age);
    }

    /**
     * @throws ReflectionException
     */
    public function test_nested_models()
    {
        $model = new AddressViewModel(new KeyValueDataSource([
            'Street' => 'Houseberry',
            'HouseNumber' => '20',
            'RegisteredUser' => [
                'FirstName' => 'Jack',
                'LastName' => 'Smith',
                'Password' => 'test',
                'RepeatedPassword' => 'test',
            ],
        ]));

        $this->assertSame('Houseberry', $model->Street);
        $this->assertSame(20, $model->HouseNumber);
        $this->assertNull($model->ParentAddress);
        $this->assertSame('Jack', $model->RegisteredUser->FirstName);
        $this->assertSame('Smith', $model->RegisteredUser->LastName);
        $this->assertNull($model->RegisteredUser->Age);
    }

    /*
        public function test_array_data_type()
        {
            $model = new ArraysViewModel(new Request([
                'IdsArray' => ['1', '2', '-3'],
                'PricesArray' => ['1.2', '3.4', '-5.6'],
                'NamesArray' => ['Jack', 'Joe', 'Jane', '']
            ]));

            $this->assertTrue($model->isValid());

            $this->assertCount(3, $model->IdsArray);
            $this->assertCount(3, $model->PricesArray);
            $this->assertCount(4, $model->NamesArray);

            $this->assertSame(1, $model->IdsArray[0]);
            $this->assertSame(2, $model->IdsArray[1]);
            $this->assertSame(-3, $model->IdsArray[2]);

            $this->assertSame(1.2, $model->PricesArray[0]);
            $this->assertSame(3.4, $model->PricesArray[1]);
            $this->assertSame(-5.6, $model->PricesArray[2]);

            $this->assertSame('Jack', $model->NamesArray[0]);
            $this->assertSame('Joe', $model->NamesArray[1]);
            $this->assertSame('Jane', $model->NamesArray[2]);
            $this->assertSame('', $model->NamesArray[3]);
        }

        public function test_carbon()
        {
            $model = new ApiTokenViewModel(new Request([
                'Value' => '0987654321234567',
                'ValidFrom' => '2017-06-01',
            ]));

            $this->assertTrue($model->isValid());
            $this->assertInstanceOf(Carbon::class, $model->ValidFrom);
            $this->assertEquals($model->ValidFrom, new Carbon('2017-06-01'));
        }

    */
}
