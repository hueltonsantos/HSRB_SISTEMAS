<?php
/**
 * Model para Configuração de Profissionais e Vínculos
 */
class ProfissionaisConfigModel extends Model {
    protected $table = 'master_profissionais_config';

    public function salvarConfiguracao($data) {
        $dados = [
            'profissional_id' => $data['profissional_id'],
            'usuario_sistema_id' => !empty($data['usuario_sistema_id']) ? $data['usuario_sistema_id'] : null,
            'repasse_padrao_percentual' => $data['repasse_padrao_percentual'] ?? 0,
            'ativo' => $data['ativo'] ?? 1,
            'data_inicio_vinculo' => $data['data_inicio_vinculo'] ?? date('Y-m-d')
        ];

        $existe = $this->getByProfissionalId($data['profissional_id']);

        if ($existe) {
            $this->db->update($this->table, $dados, 'id = ?', [$existe['id']]);
            return $existe['id'];
        } else {
            return $this->db->insert($this->table, $dados);
        }
    }

    public function getByProfissionalId($profissionalId) {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE profissional_id = ?", 
            [$profissionalId]
        );
    }

    public function getByUsuarioId($usuarioId) {
        return $this->db->fetchOne(
            "SELECT pc.*, p.nome, p.especialidade_id 
             FROM {$this->table} pc
             JOIN master_profissionais p ON pc.profissional_id = p.id
             WHERE pc.usuario_sistema_id = ? AND pc.ativo = 1", 
            [$usuarioId]
        );
    }
}
