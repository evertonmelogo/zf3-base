<?php
/**
 *
 * @link      http://github.com/realejo/libraray-zf1
 * @copyright Copyright (c) 2011-2014 Realejo (http://realejo.com.br)
 * @license   http://unlicense.org
 */
namespace Realejo;

class DateFormatter
{
    const QUARTER = 'Q';

    /**
     * Transforma data no formato d/m/a para o formato a-m-d
     *
     * @param string|Zend_Date $d data a se transformada para o formato do MYSQL
     * @return string
     */
    static public function toMySQL($d)
    {

        if (empty($d)) {
            return null;
        } else {
            if ($d instanceof \DateTime) {
                $sql = $d->format('Y-m-d H:i:s');
            } else {
                $datetime = explode(' ', $d);
                $date = explode('/', $datetime[0]);
                $sql = sprintf("%04d-%02d-%02d", $date[2], $date[1], $date[0]);

                if (isset($datetime[1])) $sql .= ' ' . $datetime[1];
            }
            return $sql;
        }
    }

    /**
     * Retorna a diferença entre duas datas ($d1-$d2)
     * Sempre calculado a partir da diferença de segundos entre as datas
     *
     * Opções para $part
     *         a - anos
     *         m - meses
     *         w - semanas
     *         d - dias
     *         h - horas
     *         n - minutos
     *         s - segundos (padrão)
     *
     * @param Zend_Date $d1
     * @param Zend_Date $d2
     * @param string $part
     * @return int
     */
    static function staticDiff(\DateTime $d1, \DateTime $d2, $part = null)
    {
        if ($d1 instanceof \DateTime)
            $d1 = $d1->getTimestamp();

        if ($d2 instanceof \DateTime)
            $d2 = $d2->getTimestamp();

        $diff = abs($d1 - $d2);

        switch ($part)
        {
            case 'y':
                return floor($diff / 31536000); # 60*60*24*365
            case 'm':
                return floor($diff / 2592000); # 60*60*24*30
            case 'w':
                return floor($diff / 604800); # 60*60*24*7
            case 'd':
                return floor($diff / 86400); # 60*60*24
            case 'h':
                return floor($diff / 3600);  # 60*60
            case 'n':
                return floor($diff / 60);
            case 's':
            default :
                return $diff;
        }
    }

    /**
     * Alterada para incluir Trimestre (Quarter)
     *
     * @param  string              $part    OPTIONAL Part of the date to return, if null the timestamp is returned
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing input
     * @return string  date or datepart
     * @return string|int
     */
    public function get($part = null, $locale = null)
    {
        if ($part === 'Q') {
            $objDateTime = new \DateTime();

            $q = $objDateTime->format("m");

            return ceil ($q / 3);
        } else {
            $objDateTime = new \DateTime();

            return $objDateTime->format($part);
        }
    }


    /**
     * Retorna os nomes dos meses
     *
     * @todo usar o Zend_Locale para recuperar os nomes dos meses
     *
     * @return array
     */
    static function getMeses()
    {
        return array(
            1  => 'Janeiro',
            2  => 'Fevereiro',
            3  => 'Março',
            4  => 'Abril',
            5  => 'Maio',
            6  => 'Junho',
            7  => 'Julho',
            8  => 'Agosto',
            9  => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        );
    }

    /**
     * Retorna o nome de um mês
     *
     * @todo usar o Zend_Locale para recuperar os nomes dos meses
     *
     * @return string
     */
    static function getMes($m)
    {
        // Recupera os meses
        $meses = self::getMeses();

        // Retorna se o mes existir
        return (isset($meses[$m])) ? $meses[$m] : null;
    }

    /**
     * Retornar qual é a semana
     *
     * @var $d Array||int
     *
     * @return string;
     */
    static public function getSemana($d = null)
    {
    	// Configura a semana
    	$nome_semana = array('domingo', 'segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado');

    	if (is_string($d)) $tempData = strlen($d) > 1 ? $d : (int) $d;

    	// Verifica se foi passado uma data
    	if (is_array($d) || is_string($tempData)) {
    		// Configura a data
    		if (is_array($d)) {
    			$temp = $d['ano']."-".$d['mes']."-".$d['dia'];

    		} else {
    			$temp = self::toMySQL($d);

    		}
    		// converte para a semnana
	        $w = date('w', strtotime($temp));

	        // Retorna qual é a semana
	        return $nome_semana[$w];

	    // Verifica se é um semana
    	} elseif (is_numeric($tempData)) {
    		return isset($nome_semana[$d]) ? $nome_semana[$d] : null;

    	} else {
    		// Retorna as semanas
    		return $nome_semana;

    	}
    }

    /**
     * Verifica se é uma string de data válida e retorna o array dela
     *
     * @param string $date
     * @param string $format OPCIONAL
     * @return array|NULL
     */
    static function getData($date, $format = 'dd/MM/yyyy')
    {
        if (!$date instanceof \DateTime) {
            $date = \DateTime::createFromFormat($format, $date);
        }

        if (!empty($date) && self::isDate($date, $format)) {
            return getdate($date->getTimestamp());
        }

        return null;
    }

    /**
     * Validate if a date is valid and is in the given format
     *
     * @param string $date
     * @param string $format
     *
     * @return boolean
     */
    static public function isDate($date, $format = 'm/d/Y')
    {
        $dateTime = \DateTime::createFromFormat($format, $date);

        // Verifica se apareceu algum erro
        $errors = \DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return false;
        }
        return $dateTime !== false;
    }
}