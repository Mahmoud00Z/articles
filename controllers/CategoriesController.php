<?php 

require(__DIR__ . "/../connection/connection.php");
require(__DIR__ . "/../services/ResponseService.php");

class CategoryController
{
    public function getAllCategories()
    {
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $res = $mysqli->query("SELECT id, name FROM categories");
            $cats = $res->fetch_all(MYSQLI_ASSOC);
            echo ResponseService::success_response($cats, 200);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function getCategoryById(int $id)
    {
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $stmt = $mysqli->prepare("SELECT id, name FROM categories WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $cat = $stmt->get_result()->fetch_assoc();

            if (! $cat) {
                throw new Exception('Category not found', 404);
            }

            echo ResponseService::success_response($cat, 200);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function createCategory()
    {
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input['name'])) {
                throw new Exception('Name is required', 422);
            }

            $stmt = $mysqli->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param('s', $input['name']);
            $stmt->execute();

            if ($stmt->affected_rows < 1) {
                throw new Exception('Insert failed', 500);
            }

            $newId = $stmt->insert_id;
            $stmt2 = $mysqli->prepare("SELECT id, name FROM categories WHERE id = ?");
            $stmt2->bind_param('i', $newId);
            $stmt2->execute();
            $cat = $stmt2->get_result()->fetch_assoc();

            echo ResponseService::success_response($cat, 201);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function updateCategory(int $id)
    {
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input['name'])) {
                throw new Exception('Name is required', 422);
            }

            $stmt = $mysqli->prepare(
                "UPDATE categories SET name = ? WHERE id = ?"
            );
            $stmt->bind_param('si', $input['name'], $id);
            $stmt->execute();

            if ($stmt->affected_rows < 1) {
                throw new Exception('No update made (invalid id or same name)', 400);
            }

            $stmt2 = $mysqli->prepare("SELECT id, name FROM categories WHERE id = ?");
            $stmt2->bind_param('i', $id);
            $stmt2->execute();
            $cat = $stmt2->get_result()->fetch_assoc();

            echo ResponseService::success_response($cat, 200);
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }

    public function deleteAllCategories()
    {
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $mysqli->query("DELETE FROM categories");
            echo ResponseService::success_response(
                ['message' => 'All categories deleted'],
                200
            );
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                500
            );
        }
    }

    public function deleteCategoryById(int $id)
    {
        global $mysqli;
        header('Content-Type: application/json');

        try {
            $stmt = $mysqli->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();

            if ($stmt->affected_rows < 1) {
                throw new Exception('Category not found', 404);
            }

            echo ResponseService::success_response(
                ['message' => 'Category deleted'],
                200
            );
        } catch (Exception $e) {
            echo ResponseService::error_response(
                $e->getMessage(),
                $e->getCode() ?: 500
            );
        }
    }
}
