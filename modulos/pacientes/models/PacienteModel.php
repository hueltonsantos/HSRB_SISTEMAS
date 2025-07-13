<?php
/**
 * Classe PacienteModel - Gerencia operações relacionadas aos pacientes
 */
class PacienteModel extends Model {
    
    /**
     * Construtor
     */
    public function __construct() {
        parent::__construct();
        $this->table = 'pacientes';
    }
    
    /**
     * Valida os dados do paciente
     * @param array $data
     * @return array [success, message, errors]
     */
    public function validate($data) {
        $errors = [];
        
        // Validações básicas
        if (empty($data['nome'])) {
            $errors['nome'] = 'O nome é obrigatório';
        }
        
        if (empty($data['data_nascimento'])) {
            $errors['data_nascimento'] = 'A data de nascimento é obrigatória';
        } else if (!$this->validateDate($data['data_nascimento'])) {
            $errors['data_nascimento'] = 'Data de nascimento inválida';
        }
        
        if (empty($data['cpf'])) {
            $errors['cpf'] = 'O CPF é obrigatório';
        } else if (!$this->validateCPF($data['cpf'])) {
            $errors['cpf'] = 'CPF inválido';
        } else {
            // Verificar se o CPF já existe (exceto para atualizações)
            $conditions = ['cpf' => $this->formatCPF($data['cpf'])];
            
            if (isset($data['id']) && !empty($data['id'])) {
                $existingPatient = $this->getOneWhere(['cpf' => $this->formatCPF($data['cpf'])]);
                if ($existingPatient && $existingPatient['id'] != $data['id']) {
                    $errors['cpf'] = 'Este CPF já está cadastrado para outro paciente';
                }
            } else {
                if ($this->exists($conditions)) {
                    $errors['cpf'] = 'Este CPF já está cadastrado';
                }
            }
        }
        
        if (empty($data['sexo'])) {
            $errors['sexo'] = 'O sexo é obrigatório';
        }
        
        if (empty($data['cidade'])) {
            $errors['cidade'] = 'A cidade é obrigatória';
        }
        
        if (empty($data['estado'])) {
            $errors['estado'] = 'O estado é obrigatório';
        }
        
        if (empty($data['celular'])) {
            $errors['celular'] = 'O número de celular é obrigatório';
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
        // Formata CPF
        if (isset($data['cpf'])) {
            $data['cpf'] = $this->formatCPF($data['cpf']);
        }
        
        // Formata RG
        if (isset($data['rg'])) {
            $data['rg'] = preg_replace('/[^0-9X]/', '', $data['rg']);
        }
        
        // Formata data de nascimento
        if (isset($data['data_nascimento'])) {
            // Converte do formato brasileiro para o formato do MySQL
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $data['data_nascimento'], $matches)) {
                $data['data_nascimento'] = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
            }
        }
        
        // Formata telefones
        if (isset($data['telefone_fixo'])) {
            $data['telefone_fixo'] = preg_replace('/[^0-9]/', '', $data['telefone_fixo']);
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
     * Salva os dados do paciente após validação e formatação
     * @param array $data
     * @return array [success, message, id]
     */
    public function savePaciente($data) {
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
                'message' => 'Paciente salvo com sucesso',
                'id' => $id
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao salvar paciente: ' . $e->getMessage(),
                'errors' => ['database' => $e->getMessage()],
                'id' => null
            ];
        }
    }
    
    /**
     * Busca pacientes com filtros
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchPacientes($filters = [], $limit = null, $offset = null) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        // Filtro por nome
        if (!empty($filters['nome'])) {
            $sql .= " AND nome LIKE ?";
            $params[] = "%" . $filters['nome'] . "%";
        }
        
        // Filtro por CPF
        if (!empty($filters['cpf'])) {
            $sql .= " AND cpf LIKE ?";
            $params[] = "%" . $this->formatCPF($filters['cpf']) . "%";
        }
        
        // Filtro por data de nascimento
        if (!empty($filters['data_nascimento'])) {
            $sql .= " AND data_nascimento = ?";
            
            // Converte do formato brasileiro para o formato do MySQL
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $filters['data_nascimento'], $matches)) {
                $params[] = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
            } else {
                $params[] = $filters['data_nascimento'];
            }
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
        
        // Filtro por convênio
        if (!empty($filters['convenio'])) {
            $sql .= " AND convenio LIKE ?";
            $params[] = "%" . $filters['convenio'] . "%";
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
     * Formata o CPF para o padrão XXX.XXX.XXX-XX
     * @param string $cpf
     * @return string
     */
    private function formatCPF($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Se o CPF tiver 11 dígitos, formata
        if (strlen($cpf) === 11) {
            return substr($cpf, 0, 3) . '.' . 
                   substr($cpf, 3, 3) . '.' . 
                   substr($cpf, 6, 3) . '-' . 
                   substr($cpf, 9, 2);
        }
        
        return $cpf;
    }
    
    /**
     * Valida um CPF
     * @param string $cpf
     * @return bool
     */
    private function validateCPF($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se o CPF tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }
        
        // Calcula o primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += (int) $cpf[$i] * (10 - $i);
        }
        
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : 11 - $resto;
        
        // Verifica o primeiro dígito verificador
        if ($cpf[9] != $dv1) {
            return false;
        }
        
        // Calcula o segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += (int) $cpf[$i] * (11 - $i);
        }
        
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : 11 - $resto;
        
        // Verifica o segundo dígito verificador
        if ($cpf[10] != $dv2) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida uma data
     * @param string $date
     * @return bool
     */
    private function validateDate($date) {
        if (empty($date)) {
            return false;
        }
        
        // Verifica formato brasileiro (dd/mm/yyyy)
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
            $day = (int) $matches[1];
            $month = (int) $matches[2];
            $year = (int) $matches[3];
            
            return checkdate($month, $day, $year);
        }
        
        // Verifica formato ISO (yyyy-mm-dd)
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
            $day = (int) $matches[3];
            
            return checkdate($month, $day, $year);
        }
        
        return false;
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