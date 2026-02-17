<?php
class PerfilModel extends Model {
    protected $table = 'perfis';

    public function getPermissions($perfilId) {
        $sql = "SELECT permissao_id FROM perfil_permissoes WHERE perfil_id = ?";
        return $this->db->fetchAll($sql, [$perfilId]);
    }

    public function getAllPermissions() {
        return $this->db->fetchAll("SELECT * FROM permissoes ORDER BY nome");
    }

    public function updatePermissions($perfilId, $permissions) {
        // Clear existing
        $this->db->delete('perfil_permissoes', 'perfil_id = ?', [$perfilId]);
        
        // Add new
        foreach ($permissions as $permId) {
            $this->db->insert('perfil_permissoes', [
                'perfil_id' => $perfilId,
                'permissao_id' => $permId
            ]);
        }
    }
}
