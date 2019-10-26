<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 10/08/2019
 * Time: 03:29
 */

namespace App\BankuModule\DataSource\Mapper;

use App\BankuModule\Entity\PersonTrait;

trait PersonHydratorTrait
{

    /**
     * @param PersonTrait $person
     * @param array $input
     */
    protected function createPerson(PersonTrait $person, array $input)
    {
        if (isset($input['first_name'])) {
            $person->setFirstName($input['first_name']);
        }
        if (isset($input['last_name'])) {
            $person->setLastName($input['last_name']);
        }

        foreach (['address', 'email', 'phone'] as $field) {
            if (isset($input[$field])) {
                $person->{'set' . ucfirst($field)}($input[$field]);
            }
        }
    }

    /**
     * @param PersonTrait $person
     * @return array
     */
    protected function extractPerson(PersonTrait $person): array
    {
        return [
            'first_name' => $person->getFirstName(),
            'last_name' => $person->getLastName(),
            'address' => $person->getAddress(),
            'email' => $person->getEmail(),
            'phone' => $person->getPhone()
        ];
    }
}
