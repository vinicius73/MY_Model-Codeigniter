<?php

/*
 * @author luiz.vinicius73@gmail.com
 */
class MY_Model extends CI_Model {

    private $Class = NULL;
    
    protected $Dados = NULL;

    public function __construct() {
        parent::__construct();
        $this->GetClass();
    }

    //Verifica Tipo de Váriavel
    public static function GetTipo($VAR = NULL) {
        $R = NULL;
        //Se váriavel é do Tipo Numerica
        if (is_numeric($VAR)) {
            $R = 'integer';
            //Se não Encontre
        } else {
            $R = gettype($VAR);
        }
        return $R;
    }

    public function Get($criterio = NULL) {
        $Retorno = FALSE;
        switch (self::GetTipo($criterio)) {
            //Caso seja um array busca via criterio
            case 'array':
                $this->Dados = $this->GetByCriterio($criterio);
                break;
            //Caso seja um numero pega pelo ID
            case 'integer':
                $this->Dados = $this->GetByID($criterio);
                break;
            case 'string':
                $this->Dados = $this->GetByString($criterio);
                break;
        }
        //Se n'ao estiverem vazios
        if (!empty($this->Dados)) {
            $Retorno = $this->Dados;
            unset($this->Dados);
        }

        return $Retorno;
    }

    public function Salva($dados = array(), $criterio = NULL) {
        //PEGA CLASSE ATUAL
        $Class = $this->GetClass();
        //----
        $R = FALSE;
        if (is_array($dados) && !empty($dados)) {
            if (!is_null($criterio) && !empty($criterio)) {
                $R = $this->Atualiza($dados, $criterio);
            } else {
                $this->db->insert($Class::TABELA, $dados);
                $R = TRUE;
            }
        }
        return $R;
    }

    public function GetAll($page = 0, $paginacao = 25) {
        //PEGA CLASSE ATUAL
        $Class = $this->GetClass();
        //----
        $Retorno = NULL;
        if ($page == TRUE) {
            $Query = $this->db->get($Class::TABELA);
            $Retorno = $Query->result_array();
        }
        return $Retorno;
    }

    //GetByCriterio
    private function GetByCriterio($criterio = NULL) {
        //PEGA CLASSE ATUAL
        $Class = $this->GetClass();
        //----
        $this->db->where($criterio);
        $Query = $this->db->get($Class::TABELA);
        return $Query->result_array();
    }

    //GetByID
    private function GetByID($ID) {
        //PEGA CLASSE ATUAL
        $Class = $this->GetClass();
        //----
        $this->db->where($Class::ID, $ID);
        $Query = $this->db->get($Class::TABELA);
        return $Query->result_array();
    }

    //Atualiza
    private function Atualiza($dados = array(), $criterio = NULL) {
        //PEGA CLASSE ATUAL
        $Class = $this->GetClass();
        //----
        $ERRO = FALSE;
        //Determina Filtro
        switch (self::GetTipo($criterio)) {
            case 'integer':
                $this->db->where($Class::ID, $criterio);
                break;
            case 'array':
                $this->db->where($criterio);
                break;
            case 'string':
                $this->db->where($Class::Slug, $criterio);
                break;
            default :
                $ERRO = TRUE;
                break;
        }
        //Valida ERRO
        if (!$ERRO) {
            $this->db->update($Class::TABELA, $dados);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    private function GetClass() {
        if (is_null($this->Class)) {
            $this->Class = get_class($this);
        }
        return $this->Class;
    }

}
/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */
