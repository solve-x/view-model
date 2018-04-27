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

        public function test_nested_view_model()
        {
            $model = new NestedViewModel(new KeyValueDataSource([
                'ID' => null,
                'FirstName' => 'Jack',
                'LastName' => null,
                'Age' => '26',
                'Address' => [
                    'Street' => 'Arsenal Way',
                    'HouseNumber' => '15asd',
                    'ParentAddress' => [
                        'Street' => 'Nekaj',
                        'HouseNumber' => '15C',
                        'RegisteredUser' => [
                            'FirstName' => null,
                            'LastName' => 'Chech',
                            'Age' => '15'
                        ]
                    ],
                    'RegisteredUser' => [
                        'FirstName' => 'Olivier',
                        'LastName' => null,
                        'Age' => '16'
                    ]
                ]
            ]));

            $this->assertFalse($model->isValid());

            $this->assertEmpty($model->LastName);
            $this->assertEmpty($model->Age);
            $this->assertNotEmpty($model->Address);
            $this->assertNotEmpty($model->Address->RegisteredUser);
            $this->assertNotEmpty($model->Address->ParentAddress);
            $this->assertNotEmpty($model->Address->ParentAddress->RegisteredUser);
            $this->assertEmpty($model->Address->HouseNumber);
            $this->assertEmpty($model->Address->RegisteredUser->LastName);
            $this->assertEmpty($model->Address->RegisteredUser->Age);
            $this->assertEmpty($model->Address->ParentAddress->HouseNumber);
            $this->assertEmpty($model->Address->ParentAddress->RegisteredUser->FirstName);
            $this->assertEmpty($model->Address->ParentAddress->RegisteredUser->Age);

            $errors = $model->getErrors();
            $this->assertCount(4, $errors);

            $this->assertEquals('The $value is null!', $errors['LastName'][0]);
            $this->assertEquals('Value less than min required!', $errors['Age'][0]);
            $this->assertEquals('Value not an int!', $errors['Address']['HouseNumber'][0]);
            $this->assertEquals('Value not an int!', $errors['Address']['ParentAddress']['HouseNumber'][0]);
            $this->assertEquals('The $value is null!', $errors['Address']['ParentAddress']['RegisteredUser']['FirstName'][0]);
            $this->assertEquals('Value less than min required!', $errors['Address']['ParentAddress']['RegisteredUser']['Age'][0]);
            $this->assertEquals('The $value is null!', $errors['Address']['RegisteredUser']['LastName'][0]);
            $this->assertEquals('Value less than min required!', $errors['Address']['RegisteredUser']['Age'][0]);
            $this->assertEquals('BackupAddress is required and missing!', $errors['BackupAddress'][0]);

        }
    */
}
