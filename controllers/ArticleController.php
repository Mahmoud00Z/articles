<?php 

require(__DIR__ . "/../models/Article.php");
require(__DIR__ . "/../connection/connection.php");
require(__DIR__ . "/../services/ArticleService.php");
require(__DIR__ . "/../services/ResponseService.php");

class ArticleController{
    
    public function getAllArticles(){
        global $mysqli;
        header('Content-Type: application/json');

         try {
            $stmt = $mysqli->prepare("SELECT * FROM articles WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $article = $stmt->get_result()->fetch_assoc();

            if (! $article) {
                throw new Exception('Not found', 404);
            }

            echo ResponseService::success_response($article, 200);

        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function deleteAllArticles(){
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $mysqli->query("DELETE * FROM articles");
            echo ResponseService::success_response(
                ['message'=>'All articles deleted'],
                200
            );
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                500
            );

        }
    }

    public function deleteArticle($id){
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $stmt = $mysqli->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception('Not found', 404);
            }

            echo ResponseService::success_response(['message'=>'Article deleted'], 200);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function getArticle($id){
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $stmt = $mysqli->prepare("SELECT * FROM articles WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $article = $stmt->get_result()->fetch_assoc();

            if (! $article) {
                throw new Exception('Not found', 404);
            }

            echo ResponseService::success_response($article, 200);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function createArticle($data){
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $stmt = $mysqli->prepare("INSERT INTO articles (name, author, description) VALUES (?, ?, ?)");
            $stmt->bind_param('sss',
             $data['name'],
             $data['author'],
             $data['description']);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception('Failed to create article', 500);
            }

            echo ResponseService::success_response(['message'=>'Article created'], 201);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function updateArticle($id, $data){
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $stmt = $mysqli->prepare("UPDATE articles SET name = ?, author = ?, description = ? WHERE id = ?");
            $stmt->bind_param('sssi',
             $data['name'],
             $data['author'],
             $data['description'],
             $id);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception('Not found or no changes made', 404);
            }

            echo ResponseService::success_response(['message'=>'Article updated'], 200);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function getArticlesByCategoryId(int $categoryId)
    {
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $stmt = $mysqli->prepare(
                "SELECT id, name, author, description
                   FROM articles
                  WHERE category_id = ?"
            );
            $stmt->bind_param('i', $categoryId);
            $stmt->execute();
            $articles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            echo ResponseService::success_response($articles, 200);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function getCategoryByArticleId(int $articleId)
    {
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $stmt = $mysqli->prepare(
                "SELECT c.id, c.name
                   FROM categories c
                   JOIN articles   a ON a.category_id = c.id
                  WHERE a.id = ?"
            );
            $stmt->bind_param('i', $articleId);
            $stmt->execute();
            $category = $stmt->get_result()->fetch_assoc();

            if (! $category) {
                throw new Exception('No category found for that article', 404);
            }

            echo ResponseService::success_response($category, 200);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }



}

//To-Do:

//1- Try/Catch in controllers ONLY!!! 
//2- Find a way to remove the hard coded response code (from ResponseService.php)
//3- Include the routes file (api.php) in the (index.php) -- In other words, seperate the routing from the index (which is the engine)
//4- Create a BaseController and clean some imports 
