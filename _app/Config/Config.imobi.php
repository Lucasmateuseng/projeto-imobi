<?php

    /** Nível dos usuários */
    function user_level($key = NULL)
    {
        $value = array(
            5 => 'Corretor',
            10 => 'Administrador'
        );
        if (!empty($key)) {
            return $value[$key];
        } else {
            return $value;
        }
    }

    /** Tipo dos imóveis */
    function realty_type($key = NULL)
    {
        $value = array(
            1 => 'Apartamento',
            2 => 'Area',
            3 => 'Casa',
            4 => 'Galpão',
            5 => 'Pousada',
            6 => 'Prédio',
            7 => 'Sala',
            8 => 'Terreno',
            9 => 'Chácara',
            10 => 'Fazenda', 
            11 => 'Rancho'
        );
        if (!empty($key)) {
            return $value[$key];
        } else {
            return $value;
        }
    }

    /** Finalidade */
    function realty_finality($key = NULL)
    {
        $value = array(
            1 => 'Comercial',
            2 => 'Residencial'
        );
        if (!empty($key)) {
            return $value[$key];
        } else {
            return $value;
        }
    }

    /** Tipo de transação */
    function realty_transaction($key = NULL)
    {
        $value = array(
            1 => 'Alugar',
            2 => 'Comprar',
            3 => 'Temporada'
        );
        if (!empty($key)) {
            return $value[$key];
        } else {
            return $value;
        }
    }

    /** Observações */
    function realty_obs($key = NULL)
    {
        $value = array(
            1 => 'Lançamento',
            2 => 'Reservado',
            3 => 'Alugado',
            4 => 'Vendido',
            5 => 'Indisponível'
        );
        if (!empty($key)) {
            return $value[$key];
        } else {
            return $value;
        }
    }