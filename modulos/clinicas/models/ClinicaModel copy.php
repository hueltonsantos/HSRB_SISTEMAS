<?php
/**
 * Classe ClinicaModel - Gerencia operações relacionadas às clínicas parceiras
 */
class ClinicaModel extends Model {
    
    /**
     * Construtor
     */
    public function __construct() {
        parent::__construct();
        $this->table = 'clinicas_parceiras';
    }
    
    /**
     * Valida os dados da clínica
     * @param array $data
     * @return array [success, message, errors]
     */
    public function validate($data) {
        $errors = [];
        
        // Validações básicas
        if (empty($data['nome'])) {
            $errors['nome'] = 'O nome é obrigatório';
        }
        
        if (empty($data['endereco'])) {
            $errors['endereco'] = 'O endereço é obrigatório';
        }
        
        if (empty($data['cidade'])) {
            $errors['cidade'] = 'A cidade é obrigatória';
        }
        
        if (empty($data['estado'])) {
            $errors['estado'] = 'O estado é obrigatório';
        }
        
        if (empty($data['telefone'])) {
            $errors['telefone'] = 'O telefone é obrigatório';
        }
        
        // Validar CNPJ se fornecido
        if (!empty($data['cnpj'])) {
            if (!$this->validateCNPJ($data['cnpj'])) {
                $errors['cnpj'] = 'CNPJ inválido';
            } else {
                // Verificar se o CNPJ já existe (exceto para atualizações)
                $cnpj = $this->formatCNPJ($data['cnpj']);
                
                if (isset($data['id']) && !empty($data['id'])) {
                    $existingClinica = $this->getOneWhere(['cnpj' => $cnpj]);
                    if ($existingClinica && $existingClinica['id'] != $data['id']) {
                        $errors['cnpj'] = 'Este CNPJ já está cadastrado para outra clínica';
                    }
                } else {
                    if ($this->exists(['cnpj' => $cnpj])) {
                        $errors['cnpj'] = 'Este CNPJ já está cadastrado';
                    }
                }
            }
        }
        
        // Validar e-mail se fornecido
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'E-mail inválido';
        }
        
        // Validar CEP se fornecido
        if (!empty($data['cep']) && !preg_match('/^[0-9]{5}-[0-9]{3}$/', $data['cep'])) {
            $errors['cep'] = 'CEP inválido. Utilize o formato: 00000-000';
        }
        
        $success = empty($errors);
        $message = $success ? 'Dados válidos' : 'Existem erros nos dados fornecidos';
        
        return [
            'success' => $success,
            'message' => $message,
            'errors' => $errors
        ];
    }
    
    /**
     * Formata os dados antes de salvar
     * @param array $data
     * @return array
     */
    public function formatData($data) {
        // Formata CNPJ
        if (isset($data['cnpj']) && !empty($data['cnpj'])) {
            $data['cnpj'] = $this->formatCNPJ($data['cnpj']);
        }
        
        // Formata telefones
        if (isset($data['telefone'])) {
            $data['telefone'] = preg_replace('/[^0-9]/', '', $data['telefone']);
        }
        
        if (isset($data['celular'])) {
            $data['celular'] = preg_replace('/[^0-9]/', '', $data['celular']);
        }
        
        // Formata CEP
        if (isset($data['cep'])) {
            $data['cep'] = preg_replace('/[^0-9-]/', '', $data['cep']);
        }
        
        return $data;
    }
    
    /**
     * Salva os dados da clínica após validação e formatação
     * @param array $data
     * @return array [success, message, id]
     */
    public function saveClinica($data) {
        // Validar dados
        $validation = $this->validate($data);
        if (!$validation['success']) {
            return [
                'success' => false,
                'message' => $validation['message'],
                'errors' => $validation['errors'],
                'id' => null
            ];
        }
        
        // Formatar dados
        $data = $this->formatData($data);
        
        // Salvar no banco
        try {
            $id = $this->save($data);
            return [
                'success' => true,
                'message' => 'Clínica salva com sucesso',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao salvar clínica: ' . $e->getMessage(),
                'errors' => ['database' => $e->getMessage()],
                'id' => null
            ];
        }
    }
    
    /**
     * Busca clínicas com filtros
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchClinicas($filters = [], $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        // Filtro por nome
        if (!empty($filters['nome'])) {
            $sql .= " AND nome LIKE ?";
            $params[] = "%" . $filters['nome'] . "%";
        }
        
        // Filtro por CNPJ
        if (!empty($filters['cnpj'])) {
            $sql .= " AND cnpj LIKE ?";
            $params[] = "%" . $this->formatCNPJ($filters['cnpj']) . "%";
        }
        
        // Filtro por cidade
        if (!empty($filters['cidade'])) {
            $sql .= " AND cidade LIKE ?";
            $params[] = "%" . $filters['cidade'] . "%";
        }
        
        // Filtro por estado
        if (!empty($filters['estado'])) {
            $sql .= " AND estado = ?";
            $params[] = $filters['estado'];
        }
        
        // Filtro por status (ativo/inativo)
        if (isset($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        // Ordenação
        $sql .= " ORDER BY nome ASC";
        
        // Paginação
        if ($limit) {
            $sql .= " LIMIT {$limit}";
            if ($offset) {
                $sql .= " OFFSET {$offset}";
            }
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Verifica se uma clínica possui especialidades
     * @param int $clinicaId
     * @return bool
     */
    public function hasEspecialidades($clinicaId) {
        $sql = "SELECT COUNT(*) as total FROM especialidades_clinicas WHERE clinica_id = ?";
        $result = $this->db->fetchOne($sql, [$clinicaId]);
        return (int) $result['total'] > 0;
    }
    
    /**
     * Obtém todas as especialidades de uma clínica
     * @param int $clinicaId
     * @return array
     */
    public function getEspecialidades($clinicaId) {
        $sql = "SELECT e.* FROM especialidades e
                INNER JOIN especialidades_clinicas ec ON e.id = ec.especialidade_id
                WHERE ec.clinica_id = ? AND e.status = 1 AND ec.status = 1
                ORDER BY e.nome ASC";
        return $this->db->fetchAll($sql, [$clinicaId]);
    }
    
    /**
     * Salva as especialidades de uma clínica
     * @param int $clinicaId
     * @param array $especialidades
     * @return bool
     */
    public function saveEspecialidades($clinicaId, $especialidades) {
        try {
            $this->db->beginTransaction();
            
            // Remove todas as especialidades atuais
            $sql = "DELETE FROM especialidades_clinicas WHERE clinica_id = ?";
            $this->db->query($sql, [$clinicaId]);
            
            // Adiciona as novas especialidades
            if (!empty($especialidades)) {
                foreach ($especialidades as $especialidadeId) {
                    $data = [
                        'clinica_id' => $clinicaId,
                        'especialidade_id' => $especialidadeId,
                        'status' => 1
                    ];
                    $this->db->insert('especialidades_clinicas', $data);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Formata o CNPJ para o padrão XX.XXX.XXX/XXXX-XX
     * @param string $cnpj
     * @return string
     */
    private function formatCNPJ($cnpj) {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        // Se o CNPJ tiver 14 dígitos, formata
        if (strlen($cnpj) === 14) {
            return substr($cnpj, 0, 2) . '.' . 
                   substr($cnpj, 2, 3) . '.' . 
                   substr($cnpj, 5, 3) . '/' . 
                   substr($cnpj, 8, 4) . '-' . 
                   substr($cnpj, 12, 2);
        }
        
        return $cnpj;
    }
    
    /**
     * Valida um CNPJ
     * @param string $cnpj
     * @return bool
     */
    private function validateCNPJ($cnpj) {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        // Verifica se o CNPJ tem 14 dígitos
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }
        
        // Valida primeiro dígito verificador
        $sum = 0;
        $multiplier = 5;
        
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $multiplier;
            $multiplier = ($multiplier == 2) ? 9 : $multiplier - 1;
        }
        
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        if ($cnpj[12] != $digit1) {
            return false;
        }
        
        // Valida segundo dígito verificador
        $sum = 0;
        $multiplier = 6;
        
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $multiplier;
            $multiplier = ($multiplier == 2) ? 9 : $multiplier - 1;
        }
        
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;
        
        if ($cnpj[13] != $digit2) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Formata a data para exibição
     * @param string $date
     * @param bool $withTime
     * @return string
     */
    public function formatDateForDisplay($date, $withTime = false) {
        if (empty($date)) {
            return '';
        }
        
        // Data no formato MySQL (yyyy-mm-dd)
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $date, $matches)) {
            if ($withTime && strlen($date) > 10) {
                $datetime = new DateTime($date);
                return $datetime->format('d/m/Y H:i:s');
            } else {
                return "{$matches[3]}/{$matches[2]}/{$matches[1]}";
            }
        }
        
        return $date;
    }
}