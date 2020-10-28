<?php

namespace App\Validator;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationData;

/**
 * Class CustomValidator
 *
 * @package App\Validator
 */
class CustomValidator extends \Illuminate\Validation\Validator
{
    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     *
     * @return bool
     */
    public function validateDistinctWith($attribute, $value, $parameters, $validator)
    {
        $arrayName = ValidationData::getLeadingExplicitAttributePath($this->getPrimaryAttribute($attribute));

        $attributeData = ValidationData::extractDataFromPath(
            $arrayName,
            $this->data
        );

        $attributeName = substr(strrchr($attribute, '.'), 1);
        $parameters[] = $attributeName;

        foreach ($attributeData[$arrayName] as &$item) {
            foreach ($item as $key => $value) {
                if (!in_array($key, $parameters)) {
                    unset($item[$key]);
                }
            }
            ksort($item);
        }

        foreach ($attributeData[$arrayName] as &$item) {
            $item = [$attributeName => serialize($item)];
        }

        $flattened = array_dot($attributeData);

        $counts = array_count_values($flattened);

        return $counts[$flattened[$attribute]] === 1;
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     *
     * @return bool
     */
    public function validateOldPassword($attribute, $value, $parameters)
    {
        return Hash::check($value, current($parameters));
    }

    /**
     * @param $message
     * @param $attribute
     * @param $rule
     * @param $parameters
     *
     * @return mixed
     */
    protected function replaceDistinctWith($message, $attribute, $rule, $parameters)
    {
        return str_replace([':attribute', ':params'], [
            $this->getDisplayableAttribute($attribute),
            implode(', ', $this->getAttributeList($parameters))
        ], __('validation.distinct_with'));
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     *
     * @return bool
     */
    public function validateRequiredNotPresent($attribute, $value, $parameters, $validator)
    {
        return Arr::has($this->data, $parameters[0]) || $this->validateRequired($attribute, $value);
    }

    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @param $validator
     *
     * @return bool
     */
    public function validateNotContains($attribute, $value, $parameters, $validator)
    {
        $words = [];
        $parameters = array_filter($parameters);

        foreach ($parameters as $parameter) {
            $words = array_merge($words, array_filter(array_map('trim', explode(' ', $parameter))));
        }

        foreach ($words as $word) {
            if (stripos($value, $word) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $message
     * @param $attribute
     * @param $rule
     * @param $parameters
     *
     * @return mixed
     */
    protected function replaceNotContains($message, $attribute, $rule, $parameters)
    {
        $words = [];
        $parameters = array_filter($parameters);

        foreach ($parameters as $parameter) {
            $words = array_merge($words, array_filter(array_map('trim', explode(' ', $parameter))));
        }

        return str_replace([':attribute', ':params'], [
            $this->getDisplayableAttribute($attribute),
            implode(', ', $words)
        ], __('validation.not_contains'));
    }
}
