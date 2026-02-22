-- Adiciona coluna agendamento_id na tabela guias_encaminhamento se não existir
SET @dbname = DATABASE();
SET @tablename = "guias_encaminhamento";
SET @columnname = "agendamento_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE guias_encaminhamento ADD COLUMN agendamento_id INT(11) NULL AFTER paciente_id;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Adiciona índice para performance
SET @indexName = "idx_guias_agendamento";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexName)
  ) > 0,
  "SELECT 1",
  "CREATE INDEX idx_guias_agendamento ON guias_encaminhamento(agendamento_id);"
));
PREPARE createIndex FROM @preparedStatement;
EXECUTE createIndex;
DEALLOCATE PREPARE createIndex;
