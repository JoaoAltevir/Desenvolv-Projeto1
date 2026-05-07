<?php
class TesteWifi {
    private $conn;
    private $table_name = "testes_wifi";

    public function __construct($db) {
         $this->conn = $db;
    }

    public function lerTodos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lerUm($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar($comodo, $velocidade) {
        $query = "INSERT INTO " . $this->table_name . " (comodo, velocidade_5ghz) 
                   VALUES (:comodo, :velocidade)";

        $stmt = $this->conn->prepare($query);

        $comodo     = htmlspecialchars(strip_tags($comodo));
        $velocidade = htmlspecialchars(strip_tags($velocidade));

        $stmt->bindParam(":comodo",     $comodo);
        $stmt->bindParam(":velocidade", $velocidade);

        return $stmt->execute();
    }

    public function atualizar($id, $comodo, $velocidade) {
        $query = "UPDATE " . $this->table_name . " 
                  SET comodo = :comodo, velocidade_5ghz = :velocidade 
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $id         = intval($id);
        $comodo     =   htmlspecialchars(strip_tags($comodo));
        $velocidade = htmlspecialchars(strip_tags($velocidade));

        $stmt->bindParam(":id",         $id,         PDO::PARAM_INT);
        $stmt->bindParam(":comodo",     $comodo);
        $stmt->bindParam(":velocidade", $velocidade);

        return $stmt->execute();
    }

    public function deletar($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $id = intval($id);
        $stmt->bindParam (":id", $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>