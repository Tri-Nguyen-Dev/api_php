<?php
namespace Src\TableGateways;

class PersonGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll($page,$limit,$q)
    {
        $stmt = $this->db->query('SELECT count(*) FROM users');
        $total_results = $stmt->fetchColumn();
        $total_pages = ceil($total_results / $limit);

        $page = isset($page) ? $page : 1;

        $starting_limit = ($page - 1) * $limit;

        $statement = "
            SELECT 
                id, name, phone, address, avatar, birth, description
            FROM
                users
            WHERE name LIKE '%".$q."%'
            LIMIT $starting_limit, $limit    
        ";

        try {
            $result = [];
            $statement = $this->db->query($statement);
            $data = $statement->fetchAll(\PDO::FETCH_ASSOC);

            $result["data"] = $data;
            $result["pagination"]["totalPage"] = $total_pages;
            $result["pagination"]["currentPage"] = $page;
            $result["pagination"]["totalRows"] = $total_results;

            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                id, name, phone, address, avatar, birth, description
            FROM
                users
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return ;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO users 
                (name, phone, address, avatar, birth, description)
            VALUES
                (:name, :phone, :address, :avatar, :birth, :description);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'name' => $input['name'],
                'phone'  => $input['phone'],
                'address' => $input['address'] ?? null,
                'avatar' => $input['avatar'] ?? null,
                'birth' => $input['birth'] ?? null,
                'description' => $input['description'] ?? null,
            ));
            
            return $input;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE users
            SET 
                name = :name,
                phone  = :phone,
                address = :address,
                avatar = :avatar,
                birth = :birth,
                description = :description
            WHERE id = :id;
        ";

        $data = [
            'id' => $id,
            'name' => $input['name'],
            'phone' => $input['phone'],
            'address'  => $input['address'],
            'avatar' => $input['avatar'] ?? null,
            'birth' => $input['birth'] ?? null,
            'description' => $input['description'] ?? null,
        ];

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute($data);
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function delete($id)
    {
        $statement = "
            DELETE FROM users
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }
}