<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class Commons
{
    public static function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public static function showAmount($amount)
    {
        $amount = $amount / 100;

        return self::formatPrice($amount);
    }

    public static function formatDate($value)
    {
        return Carbon::parse($value)->format('d/m/Y H:i');
    }

    public static function formatFloat($value)
    {
        $value = str_replace(".", "", $value);
        $value = str_replace(",", ".", $value);
        $value = floatval($value);

        return $value;
    }

    public static function formatPrice($value, $float = false, $decimals = 2)
    {
        if ($float)
            return floatval(sprintf("%.2f", floor($value * 100) / 100));

        return number_format(floor($value * 100) / 100, $decimals, ',', '.');
    }

    public static function formatPriceWithDecimals($value, $decimals = 2)
    {
        return number_format($value, $decimals, ',', '.');
    }

    public static function sum($val1, $val2)
    {
        return floatval(bcadd($val1, $val2, 2));
    }

    /**
     * Method responsible for multiplying without rounding
     *
     * @param $val1
     * @param $val2
     *
     * @return float
     */
    public static function multiplies($val1, $val2)
    {
        return floatval(bcmul($val1, $val2, 2));
    }

    /**
     * Method responsible for dividing without rounding
     *
     * @param $val1
     * @param $val2
     *
     * @return float
     */
    public static function divide($val1, $val2)
    {
        return floatval(bcdiv($val1, $val2, 2));
    }

    public static function timeToSeconds($time)
    {
        if (!$time) {
            return 0;
        }

        list($hours, $minutes, $seconds) = explode(":", $time);

        return $hours * 3600 + $minutes * 60 + $seconds;
    }

    public static function secondsToTime($seconds)
    {
        $minutes = intdiv($seconds, 60);
        $rest_seconds = $seconds % 60;
        $hours = intdiv($minutes, 60);
        $rest_minutes = $minutes % 60;

        $final_hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $final_minutes = str_pad($rest_minutes, 2, '0', STR_PAD_LEFT);
        $final_seconds = str_pad($rest_seconds, 2, '0', STR_PAD_LEFT);

        return $final_hours . ':' . $final_minutes . ':' . $final_seconds;
    }

    /**
     * @param $cpf_cnpj
     * @return string
     *
     * CPF: 000.000.000-00
     * CNPJ: 00.000.000/0000-00
     */
    public static function formatCpfCnpj($cpf_cnpj)
    {
        ## Retirando tudo que não for número.
        $cpf_cnpj = preg_replace("/[^0-9]/", "", $cpf_cnpj);
        $tipo_dado = NULL;
        if (strlen($cpf_cnpj) == 11) {
            $tipo_dado = "cpf";
        }
        if (strlen($cpf_cnpj) == 14) {
            $tipo_dado = "cnpj";
        }
        switch ($tipo_dado) {
            default:
                $cpf_cnpj_formatado = "Não foi possível definir tipo de dado";
                break;

            case "cpf":
                $bloco_1 = substr($cpf_cnpj, 0, 3);
                $bloco_2 = substr($cpf_cnpj, 3, 3);
                $bloco_3 = substr($cpf_cnpj, 6, 3);
                $dig_verificador = substr($cpf_cnpj, -2);
                $cpf_cnpj_formatado = $bloco_1 . "." . $bloco_2 . "." . $bloco_3 . "-" . $dig_verificador;
                break;

            case "cnpj":
                $bloco_1 = substr($cpf_cnpj, 0, 2);
                $bloco_2 = substr($cpf_cnpj, 2, 3);
                $bloco_3 = substr($cpf_cnpj, 5, 3);
                $bloco_4 = substr($cpf_cnpj, 8, 4);
                $digito_verificador = substr($cpf_cnpj, -2);
                $cpf_cnpj_formatado = $bloco_1 . "." . $bloco_2 . "." . $bloco_3 . "/" . $bloco_4 . "-" . $digito_verificador;
                break;
        }
        return $cpf_cnpj_formatado;
    }

    public static function generateTeamInitials($teamName)
    {
        $words = explode(' ', $teamName); // Divide o nome do time em palavras
        $numWords = count($words);
        $initials = '';

        if ($numWords === 1) {
            $initials = strtoupper(substr($words[0], 0, 2)); // Obtém as duas primeiras letras da única palavra
        } else {
            if ($numWords > 2) {
                for ($i = 0; $i < count($words); $i++) {
                    if (strlen($words[$i]) < 3) {
                        unset($words[$i]); // Remove a palavra com menos de três letras
                    }
                }
            }

            foreach ($words as $word) {
                $initials .= strtoupper($word[0]); // Obtém a primeira letra de cada palavra e a concatena
            }
        }

        // Limita a sigla a um máximo de duas letras
        $initials = substr($initials, 0, 2);

        // Verifica se a sigla está vazia e define uma sigla padrão
        if (empty($initials)) {
            $initials = 'NA';
        }

        return $initials;
    }


    public static function getValueCafInfo($array, $key)
    {
        $aux = collect($array)->where('key', $key)->first();

        return $aux ? $aux['value'] : '';
    }

    public static function onlyNumbers($number)
    {
        return preg_replace('/[^0-9]/', '', $number);
    }

    public static function existsKey(array $array, $key)
    {
        return array_key_exists($key, $array);
    }

    public static function existsKeyRecursive(array $array, $key, $returnData = false)
    {
        if (array_key_exists($key, $array)) {
            return true;
        }

        $keys = explode('.', $key);

        $element = $array;
        $count = 0;

        foreach ($keys as $item) {
            if (!array_key_exists($keys[$count], $element))
                return false;

            $element = $element[$keys[$count]];
            $count++;
        }

        return $returnData ? $element : true;
    }

    public static function checkJsonProperties(object $target, array $expectedProperties)
    {
        $missingProperties = [];

        foreach ($expectedProperties as $property) {
            if (!property_exists($target, $property)) {
                $missingProperties[] = $property;
            }
        }
        return $missingProperties;
    }

    public static function formatPhoneNumber(string $phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        $phoneNumber = '+55' . $phoneNumber;

        return $phoneNumber;
    }

    /**
     * Method responsible translate  dates eng to portuguese Brazil
     *
     * @param array $dates
     * @return string
     */
    public static function translanteMonths(string $date)
    {
        $dates = array(
            "January" => "Janeiro",
            "February" => "Fevereiro",
            "March" => "Março",
            "April" => "Abril",
            "May" => "Maio",
            "June" => "Junho",
            "July" => "Julho",
            "August" => "Agosto",
            "September" => "Setembro",
            "October" => "Outubro",
            "November" => "Novembro",
            "December" => "Dezembro",
        );

        $monthptBR = strtr($date, $dates);

        return $monthptBR;
    }

    public static function generatePassword($passwordLength = 8)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$&+=';
        $password = '';

        for ($i = 0; $i < $passwordLength; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $password;
    }
}
