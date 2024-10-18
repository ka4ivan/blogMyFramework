<?php

namespace application\models;

use application\core\Model;

class Admin extends Model
{
    public $error;

    public function loginValidate($post)
    {
        $config = require 'application/config/admin.php';
        if ($config['login'] != $post['login'] or $config['password'] != $post['password'])
        {
            $this->error = 'error login or password';
            return false;
        }
        return true;
    }

    public function postValidate($post, $type)
    {
        $nameLen = iconv_strlen($post['name']);
        $descriptionLen = iconv_strlen($post['description']);
        $textLen = iconv_strlen($post['text']);
        if ($nameLen < 3 or $nameLen > 100)
        {
            $this->error = "Занадто коротке ім'я(3-100 символів)";
            return false;
        }
        elseif ($descriptionLen < 3 or $descriptionLen > 100)
        {
            $this->error = "Занадто короткй опис(3-100 символів)";
            return false;
        }
        elseif ($textLen < 10 or $textLen > 5000)
        {
            $this->error = "Занадто короткий текст(10-5000 символів)";
            return false;
        }
        if (empty($_FILES['img']['tmp_name']) and $type == 'add')
        {
            $this->error = 'image not select';
            return false;
        }
        return true;
    }

    public function postAdd($post) {
        $params = [
            'name' => $post['name'],
            'description' => $post['description'],
            'text' => $post['text'],
        ];
        $this->db->query("INSERT INTO posts (name, description, text) VALUES (:name, :description, :text)", $params);
        return $this->db->lastInsertId();
    }

    public function postEdit($post, $id) {
        $params = [
            'id' => $id,
            'name' => $post['name'],
            'description' => $post['description'],
            'text' => $post['text'],
        ];
        $this->db->query("UPDATE posts SET name=:name, description=:description, text=:text WHERE id = :id", $params);
        return $this->db->lastInsertId();
    }

    public function postUploadImage($path, $id)
    {
        move_uploaded_file($path, 'public/materials/'.$id.'.jpg');
    }

    public function isPostExists($id)
    {
        $params =
            [
              'id' => $id
            ];
        return $this->db->column('SELECT id FROM posts WHERE id = :id', $params);
    }

    public function postDelete($id)
    {
        $params =
            [
                'id' => $id
            ];
        $this->db->query('DELETE FROM posts WHERE id = :id', $params);
        unlink('public/materials/'.$id.'.jpg');
    }

    public function postData($id)
    {
        $params =
            [
                'id' => $id
            ];
        return $this->db->row('SELECT * FROM posts WHERE id = :id', $params);
    }

}

















