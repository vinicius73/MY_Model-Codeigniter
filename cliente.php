<?php

/*
 * @author luiz.vinicius73@gmail.com
 */

class Cliente extends MY_Model {

    const TABELA = 'clientes';
    const ID = 'id_cliente';
    const fieldEmail = 'email';
    const fieldSenha = 'senha';
    const fieldStatus = 'status';
    const fieldAOuth = 'OAuth';

    public function __construct() {
        parent::__construct();
    }

    public function GetByString($Criterio) {
        $Criterio = array(self::fieldAOuth => $Criterio);
        return $this->Get($Criterio);
    }

    public function GetByEmail($Criterio = NULL) {
        //Valida Email
        if (filter_var($Criterio, FILTER_VALIDATE_EMAIL)):
            $Criterio = array(self::fieldEmail => $Criterio);
            $Dados = $this->Get($Criterio);
            if (empty($Dados)):
                $Dados = array();
            else:
                $Dados = end($Dados);
            endif;
        else:
            $Dados = FALSE;
        endif;


        return $Dados;
    }

    public function validaLogin($email = NULL, $senha = NULL) {
        $erro = NULL;
        $usuario = $this->GetByEmail($email);
        if ($usuario == FALSE):
            $erro = 'E-Mail inválido';
        elseif (empty($usuario)):
            $erro = 'Usuário inválido';
        else:
            #Bcrypt
            $this->load->library('bcrypt');
            //Valida senha e status
            if ($usuario[self::fieldStatus] == 0):
                $erro = 'Usuário inativo';
            elseif (!Bcrypt::check($senha, $usuario[self::fieldSenha])):
                $erro = 'Senha incorreta';
            endif;
        endif;

        return (empty($erro)) ? $usuario : $erro;
    }

    public function exeLogar($Criterio = NULL) {
        $hash = md5(Bcrypt::generateRandomSalt());
        if (filter_var($Criterio, FILTER_VALIDATE_EMAIL)):
            $Dados = array(self::fieldAOuth => $hash);
            $Criterio = array(self::fieldEmail => $Criterio);
            $this->Salva($Dados, $Criterio);
        endif;
        return $hash;
    }
}

/* End of file Cliente.php */
/* Location: ./application/models/Cliente.php */
