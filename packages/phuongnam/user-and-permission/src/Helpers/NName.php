<?php

namespace PhuongNam\UserAndPermission\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class NName
{
    private $formats = [
        'lf',
        'fl',
        'lmf',
        'fml'
    ];

    private $fullName;
    private $format = 'lf';
    private $attributes = [
        'first_name' => '',
        'middle_name' => '',
        'last_name' => '',
    ];

    private function __construct(string $fullName)
    {
        $this->fullName = $fullName;
    }

    public static function of(string $fullName)
    {
        return new static($fullName);
    }

    /**
     * Định dạng tên để tách.
     *
     * @param  string  $format
     */
    public function format(string $format)
    {
        $this->format = $format;
        $this->splitNames();

        return $this;
    }

    /**
     * Tách họ tên dựa vào định dạng.
     *
     * @return void
     */
    public function splitNames()
    {
        $arrName = explode(' ', $this->fullName);
        $strName = Str::of($this->fullName);

        if (in_array($this->format, $this->formats) && ! empty($arrName)) {
            switch ($this->format) {
                case 'lf':
                    $this->getLastFirst($strName, $arrName);
                break;
                case 'fl':
                    $this->getFirstLast($strName, $arrName);
                break;
                case 'lmf':
                    $this->getLastMiddleFirst($strName, $arrName);
                break;
                case 'fml':
                    $this->getFirstMiddleLast($strName, $arrName);
                break;
            }
        }
    }

    /**
     * Lấy họ và tên.
     *
     * @param  \Illuminate\Support\Stringable  $strName
     * @param  array  $arrName
     * @return void
     */
    public function getLastFirst(Stringable $strName, array $arrName)
    {
        if (count($arrName) < 2) {
            $this->attributes['first_name'] = $this->attributes['last_name'] = $arrName[0];
        } else {
            $this->attributes['first_name'] = $strName->after($arrName[0])->trim()->__toString();
            $this->attributes['last_name'] = $arrName[0];
        }
    }

    /**
     * Lấy tên và họ.
     *
     * @param  \Illuminate\Support\Stringable  $strName
     * @param  array  $arrName
     * @return void
     */
    public function getFirstLast(Stringable $strName, array $arrName)
    {
        if (count($arrName) < 2) {
            $this->attributes['first_name'] = $this->attributes['last_name'] = $arrName[0];
        } else {
            $this->attributes['first_name'] = $arrName[0];
            $this->attributes['last_name'] = $strName->after($arrName[0])->trim()->__toString();
        }
    }

    /**
     * Lấy họ, tên lót và tên.
     *
     * @param  \Illuminate\Support\Stringable  $strName
     * @param  array  $arrName
     * @return void
     */
    public function getLastMiddleFirst(Stringable $strName, array $arrName)
    {
        if (count($arrName) < 3) {
            $this->getLastFirst($strName, $arrName);
        } else {
            $this->attributes['first_name'] = last($arrName);
            $this->attributes['middle_name'] = $strName->between($arrName[0], last($arrName))->trim()->__toString();
            $this->attributes['last_name'] = $arrName[0];
        }
    }

    /**
     * Lấy tên, tên lót và họ.
     *
     * @param  \Illuminate\Support\Stringable  $strName
     * @param  array  $arrName
     * @return void
     */
    public function getFirsMiddletLast(Stringable $strName, $arrName)
    {
        if (count($arrName) < 3) {
            $this->getLastFirst($strName, $arrName);
        } else {
            $this->attributes['first_name'] = $arrName[0];
            $this->attributes['middle_name'] = $strName->between(last($arrName), $arrName[0])->trim()->__toString();
            $this->attributes['last_name'] = last($arrName);
        }
    }

    public function __get($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return '';
    }

    public function __toString()
    {
        return $this->fullName;
    }
}
